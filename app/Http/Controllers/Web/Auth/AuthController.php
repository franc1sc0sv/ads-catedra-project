<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\Contracts\AuthServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class AuthController extends Controller
{
    public function __construct(
        private readonly AuthServiceInterface $authService
    ) {}

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $ok = $this->authService->attemptLogin(
            (string) $request->validated('email'),
            (string) $request->validated('password'),
        );

        if (! $ok) {
            $email = (string) $request->validated('email');
            $password = (string) $request->validated('password');

            $message = $this->authService->isCredentialsValidButInactive($email, $password)
                ? 'Cuenta suspendida. Contacte al administrador.'
                : __('auth.failed');

            return back()
                ->withErrors(['email' => $message])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        Auth::user()->forceFill(['last_login_at' => now()])->save();

        return redirect()->intended(
            $this->authService->redirectPathAfterLogin(Auth::user())
        );
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout($request);

        return redirect()->route('login');
    }
}
