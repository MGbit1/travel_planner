<nav x-data="{ open: false }" class="bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="/" class="text-xl font-extrabold text-slate-800 tracking-tight hover:text-indigo-600 transition-colors">
                        TripFlow<span class="text-indigo-600">.</span>
                    </a>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="/map" :active="request()->is('map')" class="font-medium text-slate-500 hover:text-slate-900">
                        {{ __('探索地圖') }}
                    </x-nav-link>
                    
                    <x-nav-link href="/feed" :active="request()->is('feed*')" class="font-medium text-slate-500 hover:text-slate-900">
                        {{ __('靈感社群') }}
                    </x-nav-link>

                    <x-nav-link href="/ranking" :active="request()->is('ranking*')" class="font-medium text-slate-500 hover:text-slate-900">
                        {{ __('熱門榜單') }}
                    </x-nav-link>

                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="font-medium text-slate-500 hover:text-slate-900">
                        {{ __('我的行程') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-slate-200 text-sm leading-4 font-medium rounded-full text-slate-700 bg-white hover:bg-slate-50 hover:text-slate-900 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                            {{ __('個人設定') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="text-red-600 hover:text-red-700 hover:bg-red-50">
                                {{ __('登出') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @else
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-slate-900">登入</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="text-sm font-medium bg-slate-800 text-white px-4 py-2 rounded-full hover:bg-slate-700 transition-colors shadow-sm">免費註冊</a>
                    @endif
                </div>
                @endauth
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus:outline-none focus:bg-slate-100 focus:text-slate-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-slate-200">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link href="/map" :active="request()->is('map')" @click="open = false">
                {{ __('探索地圖') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="/feed" :active="request()->is('feed*')" @click="open = false">
                {{ __('靈感社群') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link href="/ranking" :active="request()->is('ranking*')" @click="open = false">
                {{ __('熱門榜單') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard*')" @click="open = false">
                {{ __('我的行程') }}
            </x-responsive-nav-link>
        </div>

        @auth
        <div class="pt-4 pb-1 border-t border-slate-100">
            <div class="px-4">
                <div class="font-medium text-base text-slate-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-slate-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')" @click="open = false">
                    {{ __('個人設定') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="text-red-600">
                        {{ __('登出') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>