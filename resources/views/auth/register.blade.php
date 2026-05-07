<x-guest-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-black text-white mb-2">Create Account</h1>
        <p class="text-slate-400 font-medium">Join us to start your digital journey.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="text-xs font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Full Name</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fa-solid fa-user"></i>
                </div>
                <input id="name" type="text" name="name" :value="old('name')" required autofocus 
                    class="block w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-slate-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none font-medium"
                    placeholder="John Doe">
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-xs font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Email Address</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <input id="email" type="email" name="email" :value="old('email')" required 
                    class="block w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-slate-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none font-medium"
                    placeholder="name@company.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="text-xs font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Password</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fa-solid fa-lock"></i>
                </div>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="block w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-slate-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none font-medium"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="text-xs font-black uppercase tracking-[0.2em] text-slate-500 ml-1">Confirm Password</label>
            <div class="relative group">
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-500 group-focus-within:text-blue-500 transition-colors">
                    <i class="fa-solid fa-shield-check"></i>
                </div>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="block w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-slate-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all outline-none font-medium"
                    placeholder="••••••••">
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-600/20 transform hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center space-x-2 group">
                <span>Create My Account</span>
                <i class="fa-solid fa-user-plus group-hover:translate-x-1 transition-transform"></i>
            </button>
        </div>

        <p class="text-center text-sm text-slate-500 font-medium">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-blue-500 hover:text-blue-400 font-bold transition-colors">Sign In</a>
        </p>
    </form>
</x-guest-layout>
