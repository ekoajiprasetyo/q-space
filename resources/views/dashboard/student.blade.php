<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('Dashboard Siswa') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-teal-600 to-cyan-600 rounded-[2.5rem] shadow-xl shadow-teal-900/10 overflow-hidden relative">
                <!-- Decorative Circles -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-60 h-60 rounded-full bg-teal-900/20 blur-3xl"></div>

                <div class="p-10 md:flex items-center justify-between relative z-10">
                    <div class="text-white">
                        <h1 class="text-3xl font-extrabold tracking-tight">Selamat datang, {{ Auth::user()->name }}!</h1>
                        <p class="mt-3 text-teal-50 text-lg font-medium">Anda memiliki <strong class="text-white">{{ $activeRequests->count() }} tugas aktif</strong> yang perlu dikerjakan saat ini.</p>
                    </div>
                    <div class="mt-8 md:mt-0">
                         <div class="inline-flex items-center justify-center p-4 bg-white/10 rounded-[2rem] backdrop-blur-md shadow-inner border border-white/20">
                            <span class="text-4xl font-extrabold text-white mr-3">{{ $activeRequests->count() }}</span>
                            <div class="text-left leading-tight">
                                <span class="text-xs text-teal-100 uppercase tracking-widest font-bold block">Tugas</span>
                                <span class="text-sm text-white font-bold block">Menunggu</span>
                            </div>
                         </div>
                    </div>
                </div>
            </div>

            <!-- Active Assignments Grid -->
            <div>
                <div class="flex items-center justify-between mb-6 px-2">
                    <h3 class="text-xl font-bold text-slate-800">Daftar Tugas Aktif</h3>
                    <div class="flex gap-2">
                         <span class="w-2 h-2 rounded-full bg-teal-500 mt-2"></span>
                         <span class="text-xs font-bold text-slate-400 uppercase tracking-wide">Prioritas</span>
                    </div>
                </div>
                
                @if($activeRequests->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($activeRequests as $request)
                            <div class="bg-white rounded-[2.5rem] shadow-sm hover:shadow-xl hover:shadow-slate-200/50 border border-slate-100 hover:border-slate-200 transition-all duration-300 overflow-hidden flex flex-col group hover:-translate-y-1">
                                <div class="p-8 flex-1">
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-teal-50 text-teal-600 group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        @if($request->deadline)
                                            <div class="flex flex-col items-end">
                                                <span class="text-[0.65rem] font-bold text-slate-400 uppercase tracking-wider mb-1">Tenggat</span>
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold {{ $request->deadline->isPast() ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }}">
                                                    {{ $request->deadline->diffForHumans() }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                    <h4 class="text-xl font-bold text-slate-900 mb-3 leading-tight">{{ $request->title }}</h4>
                                    <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed font-medium">{{ $request->description }}</p>
                                    
                                    <div class="mt-6 flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 text-xs font-bold">
                                            {{ substr($request->teacher->name, 0, 1) }}
                                        </div>
                                        <div class="text-xs font-bold text-slate-600">
                                            {{ $request->teacher->name }}
                                            <span class="block text-slate-400 font-medium">Guru Pengampu</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-2 bg-slate-50/50">
                                    <a href="#" class="block w-full text-center px-6 py-4 rounded-[2rem] bg-white border border-slate-200 text-slate-700 font-bold hover:bg-teal-600 hover:text-white hover:border-teal-600 transition-all duration-300 shadow-sm group-hover:shadow-md">
                                        Upload File Tugas
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 p-16 text-center">
                        <div class="mx-auto h-24 w-24 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-6 animate-pulse">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="mt-4 text-xl font-bold text-slate-800">Semua tugas selesai!</h3>
                        <p class="mt-2 text-slate-500 font-medium max-w-sm mx-auto">Hebat! Anda tidak memiliki tugas aktif yang perlu dikerjakan saat ini. Istirahatlah sejenak.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
