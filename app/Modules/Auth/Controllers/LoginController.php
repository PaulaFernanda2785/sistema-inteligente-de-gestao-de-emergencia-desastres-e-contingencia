<?php

namespace App\Modules\Auth\Controllers;

use App\Core\Support\AuditLogger;
use App\Core\Support\TenantContext;
use App\Http\Controllers\Controller;
use App\Modules\Auth\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
        private readonly TenantContext $tenantContext,
    ) {
    }

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = (bool) $request->boolean('remember');

        if (!Auth::attempt($credentials, $remember)) {
            $this->auditLogger->log(
                module: 'auth',
                action: 'login_failed',
                eventType: 'security_event',
                newValues: ['email' => $request->input('email')],
            );

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Credenciais inválidas.']);
        }

        $request->session()->regenerate();

        /** @var \App\Modules\Admin\Models\User $user */
        $user = Auth::user();
        $user->loadMissing('tenant');

        if ($user->status !== 'ATIVO' || $user->tenant === null || !$user->tenant->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $this->auditLogger->log(
                module: 'auth',
                action: 'login_blocked',
                eventType: 'security_event',
                newValues: ['user_id' => $user->id],
            );

            return back()->withErrors(['email' => 'Acesso bloqueado para este usuário ou tenant.']);
        }

        $user->forceFill(['last_login_at' => now()])->save();
        $this->tenantContext->setTenant($user->tenant);

        DB::table('active_sessions')->insert([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'session_token_hash' => hash('sha256', $request->session()->getId()),
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 65535),
            'last_activity_at' => now(),
            'expires_at' => now()->addMinutes((int) config('session.lifetime', 120)),
            'created_at' => now(),
        ]);

        $this->auditLogger->log(
            module: 'auth',
            action: 'login_success',
            eventType: 'security_event',
            entityType: get_class($user),
            entityId: $user->id,
        );

        return redirect()->route('admin.users.index');
    }
}
