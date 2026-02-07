<x-app-layout>
    <x-slot name="title">Ringkasan</x-slot>

    <!-- Main Container with Gradient Background -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <div class="relative items-center justify-between mb-12 isolate">
            <!-- Background Decoration -->
            <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-blue-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>
            <div class="absolute -top-20 -right-20 w-[500px] h-[500px] bg-purple-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>

            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">
                        Selamat datang, <span class="text-teal-600">{{ Auth::user()->name }}</span>.
                    </h1>
                    <p class="text-lg text-slate-500 font-medium mt-2 max-w-2xl">
                        Kelola berbagai macam produktivitas untuk mempermudah pembelajaran.
                    </p>
                </div>
                
                <!-- Date Capsule -->
                <div class="inline-flex items-center gap-2 px-5 py-2.5 bg-white rounded-full shadow-[0_2px_10px_rgb(0,0,0,0.06)] border border-slate-100">
                    <div class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></div>
                    <span class="text-sm font-bold text-slate-600">
                        {{ now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Bento Grid Layout -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            
            <!-- FILES CARD (Medium) -->
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
                <!-- Header with Color -->
                <div class="bg-blue-600 p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
                    <div class="relative z-10 flex items-start justify-between">
                         <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm text-white flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white tracking-wide">Files</h3>
                    </div>
                </div>

                <div class="p-8 pt-6 flex-1 flex flex-col justify-between bg-white relative z-10">
                    <div>
                        <p class="text-slate-500 text-sm leading-relaxed mb-4">Kelola file & tugas siswa.</p>
                        <div class="flex items-center gap-2 mb-6">
                            <span class="text-2xl font-bold text-gray-900">{{ $filesCount ?? 0 }}</span>
                            <span class="text-xs font-semibold text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full">Request</span>
                        </div>
                    </div>
                    <a href="{{ route('files.index') }}" class="btn-files block w-full text-center py-3 rounded-xl hover:rounded-full font-bold text-sm transition-all duration-300">
                        Buka Files
                    </a>
                </div>
            </div>

            <!-- PATHS CARD (Medium) -->
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
                 <!-- Header with Color -->
                <div class="bg-purple-600 p-8 relative overflow-hidden">
                     <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
                     <div class="relative z-10 flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm text-white flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                         <h3 class="text-xl font-bold text-white tracking-wide">Paths</h3>
                    </div>
                </div>
                
                <div class="p-8 pt-6 flex-1 flex flex-col justify-between bg-white relative z-10">
                    <div>
                         <p class="text-slate-500 text-sm leading-relaxed mb-4">Persingkat tautan & analitik.</p>
                         <div class="flex items-center gap-2 mb-6">
                            <span class="text-2xl font-bold text-gray-900">{{ $linksCount ?? 0 }}</span>
                            <span class="text-xs font-semibold text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full">Tautan</span>
                        </div>
                    </div>
                    <a href="{{ route('paths.index') }}" class="btn-paths block w-full text-center py-3 rounded-xl hover:rounded-full font-bold text-sm transition-all duration-300">
                        Kelola Tautan
                    </a>
                </div>
            </div>

            <!-- CODES CARD (Medium) -->
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
                 <!-- Header with Color -->
                <div class="bg-teal-600 p-8 relative overflow-hidden">
                     <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
                     <div class="relative z-10 flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm text-white flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM5 21v-4m4-4H5v4h4v-4zM12 3h.01M12 17h.01M16 3h.01M20 3h.01M16 17h.01M12 13h.01M16 13h.01M20 13h.01M20 17h.01M20 21h.01M12 21h.01M3 21h.01M3 17h.01M7 17h.01M7 21h.01M3 3h4v4H3V3zm14 0h4v4h-4V3zM3 3h4v4H3V3zm0 14h4v4H3v-4z"></path></svg>
                        </div>
                         <h3 class="text-xl font-bold text-white tracking-wide">Codes</h3>
                    </div>
                </div>

                <div class="p-8 pt-6 flex-1 flex flex-col justify-between bg-white relative z-10">
                    <div>
                        <p class="text-slate-500 text-sm leading-relaxed mb-4">Generator Kode QR Kustom.</p>
                         <div class="h-8"></div>
                    </div>
                    
                    <a href="{{ route('codes.index') }}" class="btn-codes block w-full text-center py-3 rounded-xl hover:rounded-full font-bold text-sm transition-all duration-300">
                        Buat Kode QR
                    </a>
                </div>
            </div>

            <!-- CREWS CARD (Medium) -->
            <div class="bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group flex flex-col">
                <!-- Header with Color -->
                <div class="bg-amber-500 p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-10 -mt-10 blur-xl"></div>
                    <div class="relative z-10 flex items-start justify-between">
                         <div class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-sm text-white flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white tracking-wide">Crews</h3>
                    </div>
                </div>

                <div class="p-8 pt-6 flex-1 flex flex-col justify-between bg-white relative z-10">
                    <div>
                        <p class="text-slate-500 text-sm leading-relaxed mb-4">Bagi kelompok otomatis.</p>
                         <div class="h-8"></div>
                    </div>
                    
                    <a href="{{ route('crews.index') }}" class="btn-crews block w-full text-center py-3 rounded-xl hover:rounded-full font-bold text-sm transition-all duration-300">
                        Buka Crews
                    </a>
                </div>
            </div>

        </div>
        </div>
    </div>
    
    <style>
        /* Files Button (Blue) */
        .btn-files {
            border: 2px solid #eff6ff !important; /* blue-50 */
            color: #1d4ed8 !important; /* blue-700 */
            background-color: transparent;
        }
        .btn-files:hover {
            background-color: #2563eb !important; /* blue-600 */
            border-color: #2563eb !important;
            color: #ffffff !important;
        }

        /* Paths Button (Purple) */
        .btn-paths {
            border: 2px solid #faf5ff !important; /* purple-50 */
            color: #7e22ce !important; /* purple-700 */
            background-color: transparent;
        }
        .btn-paths:hover {
            background-color: #9333ea !important; /* purple-600 */
            border-color: #9333ea !important;
            color: #ffffff !important;
        }

        /* Codes Button (Teal) */
        .btn-codes {
            border: 2px solid #f0fdfa !important; /* teal-50 */
            color: #0f766e !important; /* teal-700 */
            background-color: transparent;
        }
        .btn-codes:hover {
            background-color: #0d9488 !important; /* teal-600 */
            border-color: #0d9488 !important;
            color: #ffffff !important;
        }

        /* Crews Button (Amber) */
        .btn-crews {
            border: 2px solid #fffbeb !important; /* amber-50 */
            color: #b45309 !important; /* amber-700 */
            background-color: transparent;
        }
        .btn-crews:hover {
            background-color: #f59e0b !important; /* amber-500 */
            border-color: #f59e0b !important;
            color: #ffffff !important;
        }
    </style>
</x-app-layout>
