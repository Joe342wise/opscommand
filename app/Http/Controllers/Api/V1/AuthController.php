<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\RespondsWithApi;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use RespondsWithApi;

    public function login(Request $request): JsonResponse
    {
        $this->ensureIsNotRateLimited($request, $this->loginThrottleKey($request), 5);

        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            RateLimiter::hit($this->loginThrottleKey($request), 60);
            $this->recordAuthAudit($request, 'api.auth.login_failed', null, ['email' => $validated['email']]);

            return $this->error('Invalid credentials.', ['email' => ['The provided credentials are incorrect.']], 401);
        }

        if ($user->status !== 'active') {
            RateLimiter::hit($this->loginThrottleKey($request), 60);
            $this->recordAuthAudit($request, 'api.auth.login_blocked', $user, ['status' => $user->status]);

            return $this->error('Your account is not active.', ['email' => ['Your account is not active.']], 403);
        }

        if ($user->mfa_enabled) {
            $this->recordAuthAudit($request, 'api.auth.mfa_required', $user);

            return $this->success([
                'mfa_required' => true,
                'user_id' => $user->id,
            ], 'MFA verification is required.', 202);
        }

        RateLimiter::clear($this->loginThrottleKey($request));
        $user->update(['last_login_at' => now()]);

        $token = $user->createToken($validated['device_name'] ?? 'api')->plainTextToken;
        $this->recordAuthAudit($request, 'api.auth.login_success', $user);

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role?->name,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        $request->user()?->currentAccessToken()?->delete();
        $this->recordAuthAudit($request, 'api.auth.logout', $user);

        return $this->success(message: 'Logged out successfully.');
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        $request->user()?->currentAccessToken()?->delete();

        $token = $user->createToken('api')->plainTextToken;

        return $this->success([
            'token' => $token,
            'token_type' => 'Bearer',
        ], 'Token refreshed successfully.');
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $this->ensureIsNotRateLimited($request, $this->passwordResetThrottleKey($request), 3, 600);

        $validated = $request->validate(['email' => ['required', 'email']]);
        RateLimiter::hit($this->passwordResetThrottleKey($request), 600);

        $status = Password::sendResetLink($validated);
        $this->recordAuthAudit($request, 'api.auth.password_reset_requested', null, [
            'email' => $validated['email'],
            'status' => $status,
        ]);

        return $status === Password::RESET_LINK_SENT
            ? $this->success(message: __($status))
            : $this->error(__($status), ['email' => [__($status)]], 422);
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->mixedCase()->numbers()],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        $this->recordAuthAudit($request, 'api.auth.password_reset_completed', null, [
            'email' => $validated['email'],
            'status' => $status,
        ]);

        return $status === Password::PASSWORD_RESET
            ? $this->success(message: __($status))
            : $this->error(__($status), ['email' => [__($status)]], 422);
    }

    public function verifyMfa(): JsonResponse
    {
        return $this->error('API MFA verification is not implemented yet.', [], 501);
    }

    private function ensureIsNotRateLimited(Request $request, string $key, int $maxAttempts, int $decaySeconds = 60): void
    {
        if (! RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($key);

        throw ValidationException::withMessages([
            'email' => ["Too many attempts. Please try again in {$seconds} seconds."],
        ]);
    }

    private function loginThrottleKey(Request $request): string
    {
        return 'api-login|'.Str::lower((string) $request->input('email')).'|'.$request->ip();
    }

    private function passwordResetThrottleKey(Request $request): string
    {
        return 'api-password-reset|'.Str::lower((string) $request->input('email')).'|'.$request->ip();
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
