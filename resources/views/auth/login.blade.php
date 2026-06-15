<x-layouts.guest title="Login - OpsCommand">
    <div class="w-full max-w-sm">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-primary">OpsCommand</h1>
            <p class="text-sm text-outline mt-1">Sign in to your account</p>
        </div>
        <div class="bg-surface-container p-6 rounded-xl border border-outline-variant">
            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-on-surface mb-1.5">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary @error('email') border-danger-rose @endif">
                    @error('email')
                        <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-on-surface mb-1.5">Password</label>
                    <input type="password" id="password" name="password" required
                           class="w-full px-3 py-2 bg-surface-container-low border border-outline-variant rounded-lg text-sm text-on-surface placeholder:text-outline focus:outline-none focus:ring-1 focus:ring-primary @error('password') border-danger-rose @endif">
                </div>
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="remember" class="rounded border-outline-variant text-primary focus:ring-primary">
                        <span class="text-sm text-outline">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">Forgot password?</a>
                </div>
                <button type="submit" class="w-full py-2.5 bg-primary-container text-on-primary-container rounded-lg text-sm font-medium hover:opacity-90 transition-opacity">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</x-layouts.guest>
