<?php

namespace App\Modules\Web\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIdempotentSubmission
{
    private const SESSION_KEY = '_idempotency_processed_tokens';
    private const WINDOW_SECONDS = 5;

    public function handle(Request $request, Closure $next): Response
    {
        if (!$this->isMutableRequest($request)) {
            return $next($request);
        }

        $token = (string) ($request->input('_idempotency_token') ?? $request->header('X-Idempotency-Token', ''));
        if ($token === '') {
            return $this->buildErrorResponse(
                $request,
                'Token de idempotência ausente. Recarregue a página e tente novamente.',
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
        }

        $fingerprint = $this->buildFingerprint($request, $token);
        $processed = $this->cleanExpiredTokens((array) $request->session()->get(self::SESSION_KEY, []));

        if (array_key_exists($fingerprint, $processed)) {
            return $this->buildErrorResponse(
                $request,
                'Sua solicitação já foi recebida. Aguarde alguns segundos antes de tentar novamente.',
                Response::HTTP_CONFLICT,
            );
        }

        // Grava o token antes da execução para bloquear reenvio concorrente imediato.
        $processed[$fingerprint] = now()->timestamp;
        $request->session()->put(self::SESSION_KEY, $processed);

        /** @var Response $response */
        $response = $next($request);

        if ($response->getStatusCode() >= 400) {
            // Em erro de negócio/validação, libera o token para nova tentativa legítima.
            $processed = (array) $request->session()->get(self::SESSION_KEY, []);
            unset($processed[$fingerprint]);
            $request->session()->put(self::SESSION_KEY, $processed);
        }

        return $response;
    }

    private function isMutableRequest(Request $request): bool
    {
        return in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true);
    }

    private function buildFingerprint(Request $request, string $token): string
    {
        return hash('sha256', implode('|', [
            $request->method(),
            $request->path(),
            (string) optional($request->user())->getAuthIdentifier(),
            $token,
        ]));
    }

    /**
     * @param array<string, int> $tokens
     * @return array<string, int>
     */
    private function cleanExpiredTokens(array $tokens): array
    {
        $now = now()->timestamp;

        return array_filter(
            $tokens,
            static fn ($timestamp): bool => is_numeric($timestamp) && ($now - (int) $timestamp) < self::WINDOW_SECONDS,
        );
    }

    private function buildErrorResponse(Request $request, string $message, int $status): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
                'code' => 'idempotency_rejected',
            ], $status);
        }

        return back()
            ->withInput($request->except(['password', 'password_confirmation']))
            ->with('warning', $message);
    }
}
