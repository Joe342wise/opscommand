<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\MfaVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => __('auth.failed'),
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Your account is not active.',
            ])->onlyInput('email');
        }

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

            return redirect()->route('mfa.verify');
        }

        $request->session()->regenerate();
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
            return back()->withErrors(['code' => 'Invalid or expired verification code.']);
        }

        $verification->update(['complete_at' => now()]);

        Auth::loginUsingId($userId);
        $request->session()->regenerate();
        $request->session()->forget(['mfa_user_id', 'mfa_verification_id']);

        return redirect()->intended(route('dashboard.index'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
