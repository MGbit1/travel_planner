<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-indigo-800 leading-tight flex items-center gap-2">
            <span>🏠</span> {{ __('會員控制台') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl border border-slate-100 transform transition hover:-translate-y-1">
                <div class="p-8 md:p-12 text-slate-800 font-bold flex flex-col md:flex-row items-center gap-6">
                    <div class="text-6xl bg-blue-50 p-6 rounded-full border-4 border-white shadow-inner">🎉</div> 
                    <div>
                        <h3 class="text-2xl font-black text-indigo-700 mb-2">歡迎回來，{{ Auth::user()->name }}！</h3>
                        <p class="text-base text-slate-500 font-medium leading-relaxed">
                            您已成功登入專屬帳號。現在，您可以將辛苦排好的完美行程永久存檔囉！
                        </p>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-8 py-5 border-t border-indigo-100 flex justify-end">
                    <a href="/" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 transform hover:scale-105">
                        <span>🗺️</span> 前往地圖首頁繼續規劃
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>