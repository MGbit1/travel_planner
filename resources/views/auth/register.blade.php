<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-slate-800 tracking-tight">建立您的專屬帳號</h2>
        <p class="text-[13px] text-slate-500 mt-2 font-medium">加入 TripFlow，開始規劃下一趟完美旅程</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-[13px] font-bold text-slate-700 mb-1.5">使用者名稱</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" 
                class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm font-medium text-slate-800 placeholder:text-slate-400 transition bg-slate-50/50 focus:bg-white shadow-sm" 
                placeholder="請輸入您的暱稱">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-[12px]" />
        </div>

        <div>
            <label for="email" class="block text-[13px] font-bold text-slate-700 mb-1.5">電子郵件</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm font-medium text-slate-800 placeholder:text-slate-400 transition bg-slate-50/50 focus:bg-white shadow-sm" 
                placeholder="name@example.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-[12px]" />
        </div>

        <div>
            <label for="password" class="block text-[13px] font-bold text-slate-700 mb-1.5">密碼</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" 
                class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm font-medium text-slate-800 placeholder:text-slate-400 transition bg-slate-50/50 focus:bg-white shadow-sm" 
                placeholder="請設定至少 8 碼的密碼">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-[12px]" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-[13px] font-bold text-slate-700 mb-1.5">確認密碼</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" 
                class="w-full border border-slate-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm font-medium text-slate-800 placeholder:text-slate-400 transition bg-slate-50/50 focus:bg-white shadow-sm" 
                placeholder="請再次輸入密碼">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-[12px]" />
        </div>

        <div class="pt-4">
            <button type="submit" class="w-full bg-slate-800 text-white py-3.5 rounded-xl text-sm font-bold hover:bg-slate-700 transition-all shadow-sm hover:shadow-md flex justify-center items-center gap-2 group">
                立即註冊 <i class="bi bi-person-plus-fill transition-transform group-hover:scale-110"></i>
            </button>
        </div>

        <div class="text-center pt-5 border-t border-slate-100 mt-6">
            <p class="text-[13px] font-medium text-slate-500">
                已經有帳號了嗎？
                <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-500 font-bold transition-colors">立即登入</a>
            </p>
        </div>
    </form>
</x-guest-layout>