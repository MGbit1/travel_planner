<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">歡迎回來</h2>
        <p class="text-[13px] text-slate-500 mt-2 font-medium">登入以存取您的專屬行程與社群動態</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="block text-[13px] font-bold text-slate-700 mb-1.5">電子郵件</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm font-medium text-slate-800 placeholder:text-slate-400 transition bg-slate-50/50 focus:bg-white shadow-sm" 
                placeholder="name@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[12px]" />
        </div>

        <div>
            <div class="flex justify-between items-center mb-1.5">
                <label for="password" class="block text-[13px] font-bold text-slate-700">密碼</label>
                @if (Route::has('password.request'))
                    <a class="text-[12px] font-semibold text-indigo-600 hover:text-indigo-500 transition-colors" href="{{ route('password.request') }}">
                        忘記密碼？
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password" 
                class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm font-medium text-slate-800 placeholder:text-slate-400 transition bg-slate-50/50 focus:bg-white shadow-sm" 
                placeholder="••••••••">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[12px]" />
        </div>

        <div class="flex items-center pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-slate-800 shadow-sm focus:ring-slate-800/20 cursor-pointer" name="remember">
                <span class="ms-2 text-[13px] font-medium text-slate-500 group-hover:text-slate-700 transition-colors">保持登入</span>
            </label>
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-slate-800 text-white py-3.5 rounded-xl text-sm font-bold hover:bg-slate-700 transition-all shadow-sm hover:shadow-md flex justify-center items-center gap-2 group">
                立即登入 <i class="bi bi-arrow-right transition-transform group-hover:translate-x-1"></i>
            </button>
        </div>
        
        <div class="text-center pt-5 border-t border-slate-100 mt-6">
            <p class="text-[13px] font-medium text-slate-500">
                還沒有帳號嗎？
                <a href="{{ route('register') }}" class="text-indigo-600 hover:text-indigo-500 font-bold transition-colors">免費註冊</a>
            </p>
        </div>
    </form>
</x-guest-layout>