<x-layouts.guest title="MFA Verification - OpsCommand">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-primary">OpsCommand</h1>
            <p class="text-sm text-outline mt-1">Two-Factor Authentication</p>
        </div>
        <div class="bg-surface-container p-6 rounded-xl border border-outline-variant">
            <p class="text-sm text-on-surface mb-4">Enter the 6-digit code from your authenticator app.</p>
            <form method="POST" action="{{ route('mfa.verify') }}" class="space-y-4">
                @csrf
                <div>
                    <input type="text" name="code" maxlength="6" pattern="[0-9]{6}" required autofocus
                           class="w-full px-3 py-2.5 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface text-center tracking-[0.5em] font-mono placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary @error('code') border-danger-rose @endif"
                           placeholder="000000">
                    @error('code')
                        <p class="text-xs text-danger-rose mt-1 text-center">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full py-2.5 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                    Verify
                </button>
            </form>
        </div>
    </div>
</x-layouts.guest>
