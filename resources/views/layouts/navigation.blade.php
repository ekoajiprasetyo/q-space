<nav x-data="{ open: false, userPanelOpen: false }" class="fixed top-0 w-full z-50 transition-all duration-300 pointer-events-none md:pointer-events-auto">
    <!-- Desktop Navbar (Floating Capsule) -->
    <div class="hidden md:block max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pointer-events-auto">
        <div class="bg-white/80 backdrop-blur-md border border-white/50 shadow-lg shadow-slate-200/50 rounded-full px-6 h-16 flex items-center justify-between">
            <!-- Logo -->
            <div class="shrink-0 flex items-center gap-3">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-teal-200 group-hover:scale-105 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none" class="w-5 h-6">
                            <circle cx="50" cy="50" r="15" fill="white" />
                            <ellipse cx="50" cy="50" rx="35" ry="10" stroke="white" stroke-width="8" transform="rotate(45 50 50)" stroke-opacity="0.8"/>
                            <ellipse cx="50" cy="50" rx="35" ry="10" stroke="white" stroke-width="8" transform="rotate(-45 50 50)" stroke-opacity="0.8"/>
                        </svg>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-gray-900 group-hover:text-teal-600 transition-colors">Q-Space</span>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden md:flex items-center gap-8 border-l border-gray-100 pl-8 ml-6 h-8">
                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-sm font-bold transition-all gap-2 {{ request()->routeIs('dashboard') ? 'text-teal-600' : 'text-gray-400 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </x-nav-link>
                <x-nav-link :href="route('files.index')" :active="request()->routeIs('files.*')" class="text-sm font-bold transition-all gap-2 {{ request()->routeIs('files.*') ? 'text-blue-600' : 'text-gray-400 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                    Files
                </x-nav-link>
                <x-nav-link :href="route('paths.index')" :active="request()->routeIs('paths.*')" class="text-sm font-bold transition-all gap-2 {{ request()->routeIs('paths.*') ? 'text-purple-600' : 'text-gray-400 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                    Paths
                </x-nav-link>
                <x-nav-link :href="route('codes.index')" :active="request()->routeIs('codes.*')" class="text-sm font-bold transition-all gap-2 {{ request()->routeIs('codes.*') ? 'text-teal-600' : 'text-gray-400 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM5 21v-4m4-4H5v4h4v-4zM12 3h.01M12 17h.01M16 3h.01M20 3h.01M16 17h.01M12 13h.01M16 13h.01M20 13h.01M20 17h.01M20 21h.01M12 21h.01M3 21h.01M3 17h.01M7 17h.01M7 21h.01M3 3h4v4H3V3zm14 0h4v4h-4V3zM3 3h4v4H3V3zm0 14h4v4H3v-4z"></path></svg>
                    Codes
                </x-nav-link>
                <x-nav-link :href="route('crews.index')" :active="request()->routeIs('crews.*')" class="text-sm font-bold transition-all gap-2 {{ request()->routeIs('crews.*') ? 'text-indigo-600' : 'text-gray-400 hover:text-gray-900' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Crews
                </x-nav-link>
            </div>

            <!-- Settings Dropdown -->
            <div class="flex items-center">
                 <!-- User Profile Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-gray-100 text-sm leading-4 font-bold rounded-full text-gray-700 bg-white hover:bg-gray-50 hover:text-teal-600 focus:outline-none transition ease-in-out duration-150 shadow-sm gap-3">
                             <div class="w-8 h-8 rounded-full bg-gradient-to-r from-teal-400 to-cyan-500 flex items-center justify-center text-white text-xs">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <span class="mr-1">{{ Auth::user()->name }}</span>
                             <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();" class="text-red-600 hover:bg-red-50 hover:text-red-700 font-medium rounded-lg">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>
    </div>

    <!-- Mobile Top Navbar (Simple Capsule) -->
    <div class="md:hidden fixed top-6 inset-x-0 px-4 z-40 pointer-events-auto flex justify-center">
        <div class="w-full max-w-sm bg-white/90 backdrop-blur-md border border-white/50 shadow-lg shadow-slate-200/50 rounded-full px-4 py-2 flex items-center justify-between">
             <div class="flex items-center gap-2">
                 <div class="w-8 h-8 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-full flex items-center justify-center text-white shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none" class="w-4 h-4">
                        <circle cx="50" cy="50" r="15" fill="white" />
                        <ellipse cx="50" cy="50" rx="35" ry="10" stroke="white" stroke-width="8" transform="rotate(45 50 50)" stroke-opacity="0.8"/>
                        <ellipse cx="50" cy="50" rx="35" ry="10" stroke="white" stroke-width="8" transform="rotate(-45 50 50)" stroke-opacity="0.8"/>
                    </svg>
                </div>
                <span class="font-bold text-gray-900 text-sm">Q-Space</span>
             </div>
             
             <!-- Role Badge -->
             <span class="px-2.5 py-1 bg-teal-50 text-teal-600 rounded-full text-[10px] font-bold uppercase border border-teal-100">
                 {{ Auth::user()->role === 'guru' ? 'GURU' : 'SISWA' }}
             </span>
        </div>
    </div>

    <!-- Mobile Bottom Menu (Capsule Style) -->
    <div class="md:hidden fixed bottom-4 inset-x-0 px-4 z-[999] pointer-events-auto flex justify-center">
        
        <!-- User Panel Popup (Appears above bottom menu) -->
        <div 
            x-show="userPanelOpen" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            @click.outside="userPanelOpen = false"
            class="absolute bottom-24 bg-white rounded-2xl shadow-2xl p-4 border border-gray-100 w-full max-w-sm mb-2"
        >
            <div class="flex items-center gap-3 mb-4 p-3 bg-gray-50 rounded-xl">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-teal-400 to-cyan-500 flex items-center justify-center text-white font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div>
                     <p class="font-bold text-gray-900 line-clamp-1">{{ Auth::user()->name }}</p>
                     <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <div class="space-y-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 w-full p-3 text-left bg-white hover:bg-red-50 rounded-xl transition-colors border border-gray-100 text-red-600 font-medium group">
                         <div class="w-8 h-8 rounded-lg bg-red-50 text-red-500 group-hover:bg-white group-hover:shadow-sm flex items-center justify-center transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                         </div>
                        Keluar Aplikasi
                    </button>
                </form>
            </div>
        </div>

        <div class="w-full max-w-sm bg-slate-900/90 backdrop-blur-xl border border-white/10 shadow-2xl shadow-slate-900/50 rounded-2xl px-2 py-3 flex items-center justify-between relative z-20">
             <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" class="flex-1 flex flex-col items-center gap-1 group py-1 focus:outline-none {{ request()->routeIs('dashboard') ? 'text-teal-400' : 'text-slate-400' }}">
                <div class="p-1 rounded-xl transition-all duration-300 {{ request()->routeIs('dashboard') ? 'bg-white/10' : 'group-hover:bg-white/5' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </div>
                <span class="text-[10px] font-bold {{ request()->routeIs('dashboard') ? 'text-teal-400' : 'text-slate-500' }}">Home</span>
            </a>

            <!-- Files -->
            <a href="{{ route('files.index') }}" class="flex-1 flex flex-col items-center gap-1 group py-1 focus:outline-none {{ request()->routeIs('files.*') ? 'text-blue-400' : 'text-slate-400' }}">
                <div class="p-1 rounded-xl transition-all duration-300 {{ request()->routeIs('files.*') ? 'bg-white/10' : 'group-hover:bg-white/5' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                </div>
                <span class="text-[10px] font-bold {{ request()->routeIs('files.*') ? 'text-blue-400' : 'text-slate-500' }}">Files</span>
            </a>

            <!-- Paths -->
            <a href="{{ route('paths.index') }}" class="flex-1 flex flex-col items-center gap-1 group py-1 focus:outline-none {{ request()->routeIs('paths.*') ? 'text-purple-400' : 'text-slate-400' }}">
                <div class="p-1 rounded-xl transition-all duration-300 {{ request()->routeIs('paths.*') ? 'bg-white/10' : 'group-hover:bg-white/5' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                </div>
                 <span class="text-[10px] font-bold {{ request()->routeIs('paths.*') ? 'text-purple-400' : 'text-slate-500' }}">Paths</span>
            </a>

            <!-- Codes -->
            <a href="{{ route('codes.index') }}" class="flex-1 flex flex-col items-center gap-1 group py-1 focus:outline-none {{ request()->routeIs('codes.*') ? 'text-teal-400' : 'text-slate-400' }}">
                <div class="p-1 rounded-xl transition-all duration-300 {{ request()->routeIs('codes.*') ? 'bg-white/10' : 'group-hover:bg-white/5' }}">
                   <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM5 21v-4m4-4H5v4h4v-4zM12 3h.01M12 17h.01M16 3h.01M20 3h.01M16 17h.01M12 13h.01M16 13h.01M20 13h.01M20 17h.01M20 21h.01M12 21h.01M3 21h.01M3 17h.01M7 17h.01M7 21h.01M3 3h4v4H3V3zm14 0h4v4h-4V3zM3 3h4v4H3V3zm0 14h4v4H3v-4z"></path></svg>
                </div>
                <span class="text-[10px] font-bold {{ request()->routeIs('codes.*') ? 'text-teal-400' : 'text-slate-500' }}">Codes</span>
            </a>

            <!-- Crews -->
            <a href="{{ route('crews.index') }}" class="flex-1 flex flex-col items-center gap-1 group py-1 focus:outline-none {{ request()->routeIs('crews.*') ? 'text-indigo-400' : 'text-slate-400' }}">
                <div class="p-1 rounded-xl transition-all duration-300 {{ request()->routeIs('crews.*') ? 'bg-white/10' : 'group-hover:bg-white/5' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <span class="text-[10px] font-bold {{ request()->routeIs('crews.*') ? 'text-indigo-400' : 'text-slate-500' }}">Crews</span>
            </a>

            <!-- User Panel Trigger -->
            <button @click="userPanelOpen = !userPanelOpen" class="flex-1 flex flex-col items-center gap-1 group py-1 focus:outline-none text-slate-400">
                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-teal-400 to-cyan-500 flex items-center justify-center text-white text-[10px] font-bold shadow-md">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                 <span class="text-[10px] font-bold text-slate-500">User</span>
            </button>
        </div>
    </div>
</nav>
