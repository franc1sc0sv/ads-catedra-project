<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\Contracts\AuthServiceInterface;
use App\Services\Bitacora\Contracts\BitacoraServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService,
        private readonly BitacoraServiceInterface $bitacora,
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $email = (string) $request->validated('email');
        $password = (string) $request->validated('password');

        $ok = $this->authService->attemptLogin($email, $password);

        if (! $ok) {
            $this->bitacora->log('LOGIN_FAIL', null, 'users', null, [
                'email' => $email,
            ]);

            $message = $this->authService->isCredentialsValidButInactive($email, $password)
                ? 'Cuenta suspendida. Contacte al administrador.'
                : __('auth.failed');

            return back()
                ->withErrors(['email' => $message])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        Auth::user()->forceFill(['last_login_at' => now()])->save();

        $this->bitacora->log('LOGIN_OK', Auth::id(), 'users', (string) Auth::id(), [
            'email' => Auth::user()->email,
        ]);

        return redirect()->intended(
            $this->authService->redirectPathAfterLogin(Auth::user())
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        if ($userId !== null) {
            $this->bitacora->log('LOGOUT', $userId, 'users', (string) $userId, []);
        }

        $this->authService->logout($request);

        return redirect()->route('login');
    }
}
