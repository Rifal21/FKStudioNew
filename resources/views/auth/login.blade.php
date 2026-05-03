<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-black text-white mb-2">Welcome Back!</h1>
        <p class="text-slate-400 font-medium">Please enter your details to sign in.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-xs font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Email Address</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <input id="email" type="email" name="email" :value="old('email')" required autofocus 
                    class="block w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-slate-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none font-medium"
                    placeholder="name@company.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex justify-between items-center px-1">
                <label for="password" class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-blue-500 hover:text-blue-400 transition-colors" href="{{ route('password.request') }}">
                        Forgot?
                    </a>
                @endif
            </div>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="block w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-slate-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none font-medium"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded-lg border-white/10 bg-white/5 text-blue-600 shadow-sm focus:ring-blue-500 focus:ring-offset-0 transition-all" name="remember">
                <span class="ms-3 text-sm text-slate-400 group-hover:text-slate-300 transition-colors">{{ __('Keep me signed in') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-600/20 transform hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center space-x-2 group">
                <span>Sign In to Dashboard</span>
                <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </button>
        </div>
    </form>
</x-guest-layout>
