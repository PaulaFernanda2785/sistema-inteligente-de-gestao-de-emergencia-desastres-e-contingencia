<?php

namespace App\Modules\Auth\Controllers;

use App\Core\Support\AuditLogger;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LogoutController extends Controller
{
    public function __construct(
        private readonly AuditLogger $auditLogger,
    ) {
    }

    public function destroy(Request $request): RedirectResponse
    {
        /** @var \App\Modules\Admin\Models\User|null $user */
        $user = $request->user();

        if ($user !== null) {
            DB::table('active_sessions')
                ->where('tenant_id', $user->tenant_id)
                ->where('user_id', $user->id)
                ->where('session_token_hash', hash('sha256', $request->session()->getId()))
                ->delete();

            $this->auditLogger->log(
                module: 'auth',
                action: 'logout',
                eventType: 'security_event',
                entityType: get_class($user),
                entityId: $user->id,
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
