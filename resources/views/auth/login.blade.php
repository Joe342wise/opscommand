<x-layouts.guest title="Login - OpsCommand">
    <main class="flex-grow flex flex-col md:flex-row h-screen overflow-hidden">
        <!-- Left Panel: Branded Command Center -->
        <section class="hidden md:flex flex-1 relative flex-col justify-between p-12 bg-surface-container-lowest overflow-hidden border-r border-outline-variant">
            <div class="relative z-10 flex flex-col gap-6">
                <!-- Branding -->
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-container flex items-center justify-center rounded">
                            <span class="material-symbols-outlined text-white fill-icon">terminal</span>
                        </div>
                        <h1 class="text-headline-lg font-headline-lg text-on-surface tracking-tighter">OpsCommand</h1>
                    </div>
                    <h2 class="text-headline-md font-headline-md text-primary mt-4">Operations Command Center</h2>
                    <p class="text-body-md text-on-surface-variant max-w-md">
                        Enterprise platform for application support, incident management, operational monitoring, and shift handover coordination.
                    </p>
                </div>
                <!-- Systems Status Component -->
                <div class="mt-8 flex items-center gap-4 bg-surface-container-high border-technical p-4 rounded-lg w-fit">
                    <div class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-success-emerald opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-success-emerald"></span>
                    </div>
                    <span class="font-mono-data text-mono-data text-on-surface uppercase tracking-widest">SYSTEMS ONLINE : GLOBAL CLUSTER 08</span>
                </div>
            </div>
            <!-- Dashboard Preview Mockup -->
            <div class="relative z-10 mt-12 w-full max-w-2xl transform translate-y-12 rotate-[-1deg]">
                <div class="bg-surface-container border-technical rounded-t-xl p-3 shadow-2xl overflow-hidden aspect-video">
                    <div class="flex items-center gap-2 mb-4 border-b border-outline-variant pb-2">
                        <div class="flex gap-1.5">
                            <div class="w-2.5 h-2.5 rounded-full bg-danger-rose/30"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-warning-amber/30"></div>
                            <div class="w-2.5 h-2.5 rounded-full bg-success-emerald/30"></div>
                        </div>
                        <div class="h-4 w-32 bg-slate-800 rounded mx-auto"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 h-full opacity-60">
                        <div class="space-y-3">
                            <div class="h-20 bg-slate-800 border-technical rounded p-2">
                                <div class="w-1/2 h-2 bg-slate-700 rounded mb-2"></div>
                                <div class="w-full h-8 bg-primary/10 rounded"></div>
                            </div>
                            <div class="h-32 bg-slate-800 border-technical rounded"></div>
                        </div>
                        <div class="col-span-2 space-y-3">
                            <div class="h-8 bg-slate-800 border-technical rounded"></div>
                            <div class="h-44 bg-slate-800 border-technical rounded relative overflow-hidden">
                                <div class="absolute inset-0 p-4">
                                    <div class="w-full h-1 bg-slate-700 mb-4"></div>
                                    <div class="flex gap-2">
                                        <div class="flex-1 h-20 bg-primary/5 rounded"></div>
                                        <div class="flex-1 h-20 bg-primary/5 rounded"></div>
                                        <div class="flex-1 h-20 bg-primary/5 rounded"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Enterprise Footer Links -->
            <div class="relative z-10 flex gap-8">
                <div class="flex items-center gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[18px]">verified_user</span>
                    <span class="text-label-caps font-label-caps uppercase">End-to-End Encryption</span>
                </div>
                <div class="flex items-center gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-[18px]">history_edu</span>
                    <span class="text-label-caps font-label-caps uppercase">Audit Logging Enabled</span>
                </div>
            </div>
        </section>

        <!-- Right Panel: Authentication Form -->
        <section class="flex-grow flex items-center justify-center p-margin-page bg-surface">
            <div class="w-full max-w-md space-y-8">
                <!-- Mobile Branding (Hidden on desktop) -->
                <div class="md:hidden flex flex-col items-center gap-4 mb-10 text-center">
                    <div class="w-12 h-12 bg-primary-container flex items-center justify-center rounded">
                        <span class="material-symbols-outlined text-white fill-icon">terminal</span>
                    </div>
                    <div>
                        <h1 class="text-headline-md font-headline-md text-on-surface">OpsCommand</h1>
                        <p class="text-body-sm text-on-surface-variant">Enterprise Command Center</p>
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-headline-md font-headline-md text-on-surface">Sign In</h3>
                    <p class="text-body-sm text-on-surface-variant">Enter your credentials to access the command console.</p>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf
                    <div class="space-y-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-label-caps font-label-caps text-on-surface-variant uppercase" for="email">Email Address</label>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-outline">
                                    <span class="material-symbols-outlined text-[20px]">alternate_email</span>
                                </div>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                                       class="block w-full pl-10 bg-slate-900 border-outline-variant text-on-surface text-body-md rounded focus:ring-1 focus:ring-primary focus:border-primary transition-all duration-200 placeholder:text-outline @error('email') border-danger-rose @endif"
                                       placeholder="user.id@enterprise.domain">
                            </div>
                            @error('email')
                                <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <div class="flex justify-between items-center">
                                <label class="text-label-caps font-label-caps text-on-surface-variant uppercase" for="password">Password</label>
                                <a class="text-body-sm font-medium text-primary hover:underline transition-all" href="{{ route('password.request') }}">Forgot Password?</a>
                            </div>
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-outline">
                                    <span class="material-symbols-outlined text-[20px]">lock_open</span>
                                </div>
                                <input type="password" id="password" name="password" required
                                       class="block w-full pl-10 bg-slate-900 border-outline-variant text-on-surface text-body-md rounded focus:ring-1 focus:ring-primary focus:border-primary transition-all duration-200 placeholder:text-outline @error('password') border-danger-rose @endif"
                                       placeholder="••••••••••••">
                            </div>
                            @error('password')
                                <p class="text-xs text-danger-rose mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative flex items-center">
                                <input type="checkbox" name="remember" class="peer h-4 w-4 bg-slate-900 border-outline-variant rounded text-primary focus:ring-offset-background">
                            </div>
                            <span class="text-body-sm text-on-surface-variant group-hover:text-on-surface transition-colors">Remember my session</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-primary-container text-on-primary-container text-body-md font-semibold rounded hover:brightness-110 active:scale-[0.98] transition-all flex items-center justify-center gap-2 glow-soft">
                        <span class="material-symbols-outlined text-[20px] fill-icon">login</span>
                        Sign In to Dashboard
                    </button>

                    <div class="pt-4 flex flex-col items-center gap-4">
                        <a class="text-body-sm text-on-surface-variant hover:text-primary transition-colors flex items-center gap-2" href="#">
                            <span class="material-symbols-outlined text-[18px]">support_agent</span>
                            Contact System Administrator
                        </a>
                    </div>
                </form>

                <!-- Security Badges Footer -->
                <div class="pt-10 border-t border-outline-variant">
                    <div class="flex flex-wrap justify-center gap-4">
                        <div class="flex items-center gap-1.5 px-3 py-1 bg-surface-container-low border-technical rounded-full">
                            <span class="material-symbols-outlined text-[14px] text-success-emerald fill-icon">security</span>
                            <span class="text-mono-data text-[10px] text-on-surface-variant uppercase">Secure Access</span>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1 bg-surface-container-low border-technical rounded-full">
                            <span class="material-symbols-outlined text-[14px] text-primary fill-icon">history</span>
                            <span class="text-mono-data text-[10px] text-on-surface-variant uppercase">Audit Logging</span>
                        </div>
                        <div class="flex items-center gap-1.5 px-3 py-1 bg-surface-container-low border-technical rounded-full">
                            <span class="material-symbols-outlined text-[14px] text-warning-amber fill-icon">vibration</span>
                            <span class="text-mono-data text-[10px] text-on-surface-variant uppercase">MFA Supported</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Global System Footer -->
    <footer class="fixed bottom-0 left-0 w-full z-40 flex justify-between items-center px-margin-page h-10 bg-background border-t border-outline-variant">
        <div class="flex items-center gap-6">
            <span class="text-mono-data font-mono-data text-on-surface-variant">© 2024 OpsCommand Enterprise. All rights reserved.</span>
            <span class="text-mono-data font-mono-data text-primary/60">Terminal Session: 0x82A1</span>
        </div>
        <div class="flex items-center gap-6">
            <a class="text-label-caps font-label-caps text-on-surface-variant hover:text-primary transition-colors duration-200 uppercase" href="#">Security Policy</a>
            <a class="text-label-caps font-label-caps text-on-surface-variant hover:text-primary transition-colors duration-200 uppercase" href="#">Terms of Service</a>
            <a class="text-label-caps font-label-caps text-on-surface-variant hover:text-primary transition-colors duration-200 uppercase" href="#">System Status</a>
        </div>
    </footer>

    <script>
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('focus', () => {
                const icon = input.parentElement.querySelector('.material-symbols-outlined');
                if (icon) icon.style.color = 'var(--tw-primary)';
            });
            input.addEventListener('blur', () => {
                const icon = input.parentElement.querySelector('.material-symbols-outlined');
                if (icon) icon.style.color = '';
            });
        });
    </script>
</x-layouts.guest>
