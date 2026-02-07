<x-app-layout>
    <div class="min-h-screen bg-gray-50/50 pb-28 md:pb-12">
        <!-- Header Section with Glassmorphism -->
        <div class="relative bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 pb-32 pt-32 overflow-hidden">
            <!-- Background Decorations -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-teal-500/10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-cyan-500/10 blur-3xl"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                             <span class="inline-flex items-center rounded-full bg-teal-400/10 px-2.5 py-0.5 text-xs font-bold text-teal-300 ring-1 ring-inset ring-teal-400/20">
                                Teacher Dashboard
                            </span>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white tracking-tight">
                            Selamat Datang, {{ Auth::user()->name }}
                        </h1>
                        <p class="mt-2 text-slate-300 max-w-2xl">
                            Kelola tugas dan file siswa Anda dengan mudah dan terintegrasi langsung dengan Google Drive.
                        </p>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3 mt-4 md:mt-0">
                        @if(!$hasGoogleToken)
                            <a href="{{ route('auth.google.redirect') }}" class="group relative inline-flex justify-center items-center gap-2 px-5 py-3 bg-amber-500 hover:bg-amber-400 text-slate-900 rounded-full font-bold text-sm transition-all shadow-lg shadow-amber-900/20 hover:-translate-y-0.5 w-full md:w-auto">
                                <svg class="w-5 h-5 text-slate-900" viewBox="0 0 24 24" fill="currentColor"><path d="M12.01 1.993C6.486 2.007 2 6.55 2 12.073c0 5.523 4.418 10 9.923 10 5.505 0 9.923-4.477 9.923-10 0-5.522-4.418-10.065-9.836-10.08zM12 20.073c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8-8 8zm1-13h-2v6h2v-6zm0 8h-2v2h2v-2z" fill-opacity="0" stroke="none"/><path d="M7.75 16.5h8.5l-4.25-7.5-4.25 7.5zm.908-1.5l3.342-5.895 3.342 5.895H8.658z" fill="#0f172a"/></svg>
                                Sambungkan Drive
                            </a>
                        @else
                           <div class="flex items-center gap-2 px-4 py-2 bg-white/10 backdrop-blur-md rounded-full border border-white/5 w-full md:w-auto justify-center md:justify-start">
                                <div class="w-2.5 h-2.5 rounded-full bg-green-400 animate-pulse"></div>
                                <span class="text-xs font-bold text-white">Drive Terhubung</span>
                           </div>
                        @endif

                        <a href="{{ route('file-requests.create') }}" class="inline-flex justify-center items-center gap-2 px-6 py-3 bg-teal-500 hover:bg-teal-400 text-white rounded-full font-bold text-sm transition-all shadow-lg shadow-teal-900/20 hover:-translate-y-0.5 w-full md:w-auto">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Buat Permintaan
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content (Overlapping) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
            <!-- Stats Grid (Bento Style) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <!-- Stat 1 -->
                <div class="bg-white p-6 rounded-[2rem] shadow-sm ring-1 ring-slate-900/5 hover:shadow-md hover:scale-[1.01] transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total</span>
                    </div>
                    <div class="flex items-end gap-2">
                        <h3 class="text-4xl font-bold text-slate-900">{{ $fileRequests->total() }}</h3>
                        <span class="text-sm font-semibold text-slate-500 mb-1.5">Permintaan</span>
                    </div>
                </div>

                <!-- Stat 2 -->
                <div class="bg-white p-6 rounded-[2rem] shadow-sm ring-1 ring-slate-900/5 hover:shadow-md hover:scale-[1.01] transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-600">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Partisipasi</span>
                    </div>
                    <div class="flex items-end gap-2">
                        <h3 class="text-4xl font-bold text-slate-900">-</h3>
                        <span class="text-sm font-semibold text-slate-500 mb-1.5">Siswa Mengumpulkan</span>
                    </div>
                </div>

                 <!-- Stat 3 -->
                 <div class="bg-white p-6 rounded-[2rem] shadow-sm ring-1 ring-slate-900/5 hover:shadow-md hover:scale-[1.01] transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                        </div>
                        <span class="text-xs font-bold {{ $hasGoogleToken ? 'text-emerald-500' : 'text-amber-500' }} uppercase tracking-wider px-2 py-0.5 bg-gray-50 rounded-full">
                            {{ $hasGoogleToken ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </div>
                    @if($hasGoogleToken)
                        <div>
                            <h3 class="text-sm font-bold text-slate-500 mb-1">Terhubung ke Drive</h3>
                            <div class="mb-4">
                                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full truncate max-w-[12rem] md:max-w-[14rem]">
                                    {{ Auth::user()->email }} 
                                </span>
                            </div>
                            <a href="{{ route('auth.google.redirect') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-full text-xs font-bold transition-all w-full justify-center group">
                                <svg class="w-3 h-3 group-hover:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Ganti Akun Drive
                            </a>
                        </div>
                    @else
                        <div class="flex items-end gap-2">
                            <h3 class="text-lg font-bold text-slate-900 leading-tight">Belum Terhubung</h3>
                        </div>
                         <p class="text-xs text-slate-500 mt-2 font-medium">Hubungkan akun Google Drive untuk menyimpan file siswa.</p>
                    @endif
                </div>
            </div>

            <!-- Active File Requests -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-slate-800">Daftar Permintaan File</h2>
                 <div class="flex gap-2">
                     <button class="p-2 text-slate-400 hover:text-teal-600 bg-white rounded-xl shadow-sm hover:shadow ring-1 ring-slate-900/5 transition-all">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                     </button>
                      <button class="p-2 text-teal-600 bg-teal-50 rounded-xl shadow-sm ring-1 ring-teal-900/5 transition-all">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                     </button>
                 </div>
            </div>

            @if($fileRequests->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($fileRequests as $request)
                        <div class="group bg-white rounded-[2rem] p-6 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 ring-1 ring-slate-900/5 relative overflow-hidden">
                            <!-- Helper Colors/Blobs -->
                            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-24 h-24 rounded-full bg-gradient-to-br from-teal-50/50 to-cyan-50/50 group-hover:scale-150 transition-transform duration-500"></div>

                            <div class="relative z-10 flex flex-col h-full">
                                <div class="flex justify-between items-start mb-4">
                                    <div class="bg-indigo-50 text-indigo-600 w-12 h-12 rounded-2xl flex items-center justify-center shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold {{ $request->is_active ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20' }}">
                                        {{ $request->is_active ? 'Aktif' : 'Ditutup' }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-1 group-hover:text-teal-600 transition-colors">{{ $request->title }}</h3>
                                <p class="text-sm text-slate-500 mb-6 line-clamp-2 h-10">{{ $request->description }}</p>

                                <div class="mt-auto space-y-3">
                                    <div class="flex items-center text-xs text-slate-500 font-medium">
                                        <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ $request->deadline ? $request->deadline->format('d M Y, H:i') : 'Tanpa Tenggat' }}
                                    </div>
                                    <div class="w-full h-px bg-slate-100"></div>
                                    <div class="flex justify-between items-center pt-2">
                                        <div class="flex -space-x-2">
                                            <!-- Mock Avatars (Replace logic later) -->
                                            <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-500">?</div>
                                        </div>
                                        <a href="#" class="text-sm font-bold text-teal-600 hover:text-teal-700 hover:underline">Lihat Detail &rarr;</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    
                    <!-- New Request Card (Dotted) -->
                    <a href="{{ route('file-requests.create') }}" class="group border-2 border-dashed border-slate-300 rounded-[2rem] p-6 hover:border-teal-400 hover:bg-teal-50/30 transition-all duration-300 flex flex-col items-center justify-center text-center cursor-pointer min-h-[280px]">
                         <div class="w-16 h-16 rounded-full bg-slate-100 group-hover:bg-teal-100/50 flex items-center justify-center text-slate-400 group-hover:text-teal-600 transition-colors mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 group-hover:text-teal-700">Buat Permintaan Baru</h3>
                        <p class="text-sm text-slate-500 mt-2 max-w-[200px]">Siapkan folder pengumpulan tugas dalam hitungan detik.</p>
                    </a>
                </div>
            @else
                <div class="bg-white rounded-[2.5rem] p-12 text-center shadow-sm ring-1 ring-slate-900/5">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-3xl bg-teal-50 mb-6 animate-pulse">
                         <svg class="w-12 h-12 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900 mb-2">Mulai Perjalanan Digital Anda</h3>
                    <p class="text-slate-500 max-w-md mx-auto mb-8">Anda belum memiliki permintaan aktif. Buat permintaan pertama Anda sekarang dan lihat betapa mudahnya mengelola file siswa.</p>
                    <a href="{{ route('file-requests.create') }}" class="inline-flex items-center px-8 py-4 bg-teal-600 hover:bg-teal-500 text-white rounded-full font-bold shadow-xl shadow-teal-500/20 hover:-translate-y-1 transition-all">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Buat Permintaan Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
