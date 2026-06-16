<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\MfaVerification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->ensureIsNotRateLimited($request, $this->loginThrottleKey($request), 5);

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($this->loginThrottleKey($request), 60);
            $this->recordAuthAudit($request, 'auth.login_failed', null, ['email' => $request->input('email')]);

            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            RateLimiter::hit($this->loginThrottleKey($request), 60);
            $this->recordAuthAudit($request, 'auth.login_blocked', $user, ['status' => $user->status]);

            return back()->withErrors([
                'email' => 'Your account is not active.',
            ])->onlyInput('email');
        }

        RateLimiter::clear($this->loginThrottleKey($request));
        $user->update(['last_login_at' => now()]);

        if ($user->mfa_enabled) {
            $verification = MfaVerification::create([
                'user_id' => $user->id,
                'secret' => $user->mfa_secret,
                'code' => Str::random(6),
                'expires_at' => now()->addMinutes(5),
            ]);

            Auth::logout();
            $request->session()->put('mfa_user_id', $user->id);
            $request->session()->put('mfa_verification_id', $verification->id);
            $this->recordAuthAudit($request, 'auth.mfa_required', $user);

            return redirect()->route('mfa.verify');
        }

        $request->session()->regenerate();
        $this->recordAuthAudit($request, 'auth.login_success', $user);

        return redirect()->intended(route('dashboard.index'));
    }

    public function showMfaForm()
    {
        return view('auth.mfa');
    }

    public function verifyMfa(Request $request)
    {
        $request->validate(['code' => 'required|string|size:6']);

        $userId = $request->session()->get('mfa_user_id');
        $verificationId = $request->session()->get('mfa_verification_id');

        $verification = MfaVerification::where('id', $verificationId)
            ->where('user_id', $userId)
            ->whereNull('complete_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $verification || $verification->code !== $request->code) {
            $this->recordAuthAudit($request, 'auth.mfa_failed', $userId ? User::find($userId) : null);

            return back()->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        $verification->update(['complete_at' => now()]);

        Auth::loginUsingId($userId);
        $this->recordAuthAudit($request, 'auth.mfa_success', Auth::user());
        $request->session()->regenerate();
        $request->session()->forget(['mfa_user_id', 'mfa_verification_id']);

        return redirect()->intended(route('dashboard.index'));
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $this->recordAuthAudit($request, 'auth.logout', $user);

        return redirect()->route('login');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $this->ensureIsNotRateLimited($request, $this->passwordResetThrottleKey($request), 3, 600);

        $request->validate(['email' => 'required|email']);

        RateLimiter::hit($this->passwordResetThrottleKey($request), 600);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        $this->recordAuthAudit($request, 'auth.password_reset_requested', null, [
            'email' => $request->input('email'),
            'status' => $status,
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        $this->recordAuthAudit($request, 'auth.password_reset_completed', null, [
            'email' => $request->input('email'),
            'status' => $status,
        ]);

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    private function ensureIsNotRateLimited(Request $request, string $key, int $maxAttempts, int $decaySeconds = 60): void
    {
        if (! RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => "Too many attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    private function loginThrottleKey(Request $request): string
    {
        return Str::lower((string) $request->input('email')).'|'.$request->ip();
    }

    private function passwordResetThrottleKey(Request $request): string
    {
        return 'password-reset|'.Str::lower((string) $request->input('email')).'|'.$request->ip();
    }

    private function recordAuthAudit(Request $request, string $action, ?User $user = null, array $values = []): void
    {
        AuditLog::create([
            'actor_id' => $user?->id,
            'action' => $action,
            'entity_type' => User::class,
            'entity_id' => $user?->id ?? 0,
            'new_values' => $values ?: null,
            'ip_address' => $request->ip(),
            'user_agent' => Str::limit((string) $request->userAgent(), 255, ''),
        ]);
    }
}
