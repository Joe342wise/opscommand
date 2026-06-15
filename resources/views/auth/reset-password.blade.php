<x-layouts.guest title="Reset Password - OpsCommand">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-primary">OpsCommand</h1>
            <p class="text-sm text-outline mt-1">Set your new password</p>
        </div>
        <div class="bg-surface-container p-6 rounded-xl border border-outline-variant">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label for="email" class="block text-sm font-medium text-on-surface mb-1.5">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $email ?? '') }}" required autofocus
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary @error('email') border-danger-rose @endif">
                    @error('email')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-on-surface mb-1.5">New Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary @error('password') border-danger-rose @endif">
                    @error('password')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-on-surface mb-1.5">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary">
                </div>
                <button type="submit" class="w-full py-2.5 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</x-layouts.guest>
