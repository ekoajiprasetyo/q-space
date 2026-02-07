<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Q-Space | Organize Your Learning Universe</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-gray-50 text-gray-800 font-sans selection:bg-teal-500 selection:text-white" x-data="{ notification: true }">
        
        @if (session('status') === 'account-deleted')
            <div 
                x-show="notification"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                x-init="setTimeout(() => notification = false, 5000)"
                class="fixed top-24 right-4 z-[100] max-w-sm w-full bg-white border border-gray-100 shadow-2xl rounded-2xl p-4 flex items-center gap-4 border-l-4 border-l-red-500"
            >
                <div class="w-10 h-10 bg-red-50 rounded-full flex items-center justify-center text-red-500 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <div>
                    <h4 class="font-bold text-gray-900">Akun Dihapus</h4>
                    <p class="text-sm text-gray-500 font-medium">Akun Anda telah berhasil dihapus secara permanen.</p>
                </div>
                <button @click="notification = false" class="ml-auto text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endif
        
        <!-- Navbar -->
        <nav class="fixed w-full z-50 transition-all duration-300 bg-white/90 backdrop-blur-md border-b border-gray-200/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <div class="flex-shrink-0 flex items-center gap-3">
                        <!-- Logo Icon -->
                        <div class="w-10 h-10 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-teal-200">
                             <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none" class="w-6 h-6">
                                <circle cx="50" cy="50" r="15" fill="white" />
                                <ellipse cx="50" cy="50" rx="35" ry="10" stroke="white" stroke-width="8" transform="rotate(45 50 50)" stroke-opacity="0.8"/>
                                <ellipse cx="50" cy="50" rx="35" ry="10" stroke="white" stroke-width="8" transform="rotate(-45 50 50)" stroke-opacity="0.8"/>
                            </svg>
                        </div>
                        <div class="flex items-center gap-2">
                             <span class="font-bold text-2xl tracking-tight text-gray-900">Q-Space</span>
                             <span class="bg-blue-100 text-blue-800 text-[0.65rem] uppercase font-bold px-2 py-0.5 rounded-full border border-blue-200 tracking-wider">by Q-Link</span>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        @if (Route::has('login'))
                            @if(auth()->check() && in_array(auth()->user()->role, ['guru', 'admin']))
                                <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 text-sm font-bold text-white bg-teal-600 rounded-full hover:bg-teal-700 transition-all shadow-md shadow-teal-100">Dashboard</a>
                            @elseif(auth()->check())
                                <!-- Student (Logged In): Click triggers bounce back with error -->
                                <a href="{{ url('/dashboard') }}" class="md:hidden px-6 py-2.5 text-sm font-bold text-white bg-teal-600 rounded-full hover:bg-teal-700 transition-all shadow-md shadow-teal-100">Masuk</a>
                                <a href="{{ url('/dashboard') }}" class="hidden md:inline-block text-sm font-bold text-gray-600 hover:text-teal-600 transition-colors">Masuk</a>
                            @else
                                <!-- Guest (Not Logged In) -->
                                <a href="https://q-link.my.id/login?redirect=https://space.q-link.my.id/dashboard" class="md:hidden px-6 py-2.5 text-sm font-bold text-white bg-teal-600 rounded-full hover:bg-teal-700 transition-all shadow-md shadow-teal-100">Masuk</a>

                                <a href="https://q-link.my.id/login?redirect=https://space.q-link.my.id/dashboard" class="hidden md:inline-block text-sm font-bold text-gray-600 hover:text-teal-600 transition-colors">Masuk</a>

                                <a href="https://q-link.my.id/register?role=guru" class="hidden md:inline-flex items-center px-6 py-2.5 text-sm font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-full shadow-md shadow-teal-100 hover:-translate-y-0.5 transition-all">
                                    Daftar Sekarang
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-28 pb-24 lg:pt-40 lg:pb-32 overflow-hidden bg-white">
            <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] opacity-40"></div>
            
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <!-- Text Content -->
                    <div class="text-left animate-fade-in-up">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-teal-50 border border-teal-100 text-teal-700 text-sm font-bold mb-6">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-teal-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-teal-500"></span>
                            </span>
                            Organize Your Learning Universe.
                        </div>
                        
                        <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight text-gray-900 mb-6 leading-[1.15]">
                            Files. Paths.<br>
                            Codes. <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-500 to-cyan-600">Crews.</span>
                        </h1>
                        
                        <p class="text-lg text-gray-600 mb-8 leading-relaxed max-w-lg font-medium">
                            Kelola file Google Drive, persingkat link materi, dan buat QR code dalam satu dashboard intuitif.
                        </p>

                        <div class="flex flex-col sm:flex-row gap-4 w-full">
                            <a href="https://q-link.my.id/register?role=guru" class="inline-flex justify-center items-center px-8 py-4 text-base font-bold text-white bg-teal-600 hover:bg-teal-700 rounded-full shadow-xl shadow-teal-200 hover:-translate-y-1 transition-all w-full sm:w-auto">
                                Buat Akun
                            </a>
                            <a href="#features" class="inline-flex justify-center items-center px-8 py-4 text-base font-bold text-gray-700 bg-white border border-gray-200 rounded-full hover:bg-gray-50 hover:border-gray-300 shadow-sm transition-all hover:-translate-y-1 w-full sm:w-auto">
                                Pelajari Fitur
                            </a>
                        </div>
                    </div>

                    <!-- Hero Visual / Mockup -->
                    <div class="relative lg:h-[600px] flex items-center justify-center perspective-1000">
                         <!-- Decorative Blobs -->
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-to-tr from-teal-200 to-purple-200 rounded-full blur-[120px] opacity-20 animate-pulse"></div>

                         <!-- Orbital Visual -->
                        <div class="relative w-full max-w-lg mx-auto h-[500px] flex items-center justify-center transform hover:scale-105 transition-transform duration-700 ease-out">
                            <!-- Center Planet -->
                            <div class="w-40 h-40 bg-white rounded-full shadow-2xl flex items-center justify-center z-20 relative">
                                <div class="absolute inset-0 bg-gradient-to-br from-teal-400 to-cyan-500 opacity-20 rounded-full blur-2xl"></div>
                                <div class="w-32 h-32 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-full flex items-center justify-center text-white shadow-inner relative overflow-hidden group hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_30%,rgba(255,255,255,0.4),transparent)]"></div>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none" class="w-20 h-20 drop-shadow-lg">
                                        <circle cx="50" cy="50" r="15" fill="white" class="animate-pulse"/>
                                        <ellipse cx="50" cy="50" rx="35" ry="10" stroke="white" stroke-width="6" transform="rotate(45 50 50)" stroke-opacity="0.9"/>
                                        <ellipse cx="50" cy="50" rx="35" ry="10" stroke="white" stroke-width="6" transform="rotate(-45 50 50)" stroke-opacity="0.9"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Orbit Ring -->
                            <div class="absolute inset-0 border border-gray-200/60 rounded-full w-[450px] h-[450px] m-auto border-dashed animate-[spin_30s_linear_infinite]"></div>
                            <div class="absolute inset-0 border border-teal-100/40 rounded-full w-[600px] h-[600px] m-auto border-dotted animate-[spin_40s_linear_infinite_reverse]"></div>

                            <!-- Satellite 1: Files (Top) -->
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-6 w-20 h-20 bg-white rounded-3xl shadow-xl flex items-center justify-center border border-blue-50 z-10 animate-[bounce_4s_infinite]">
                                <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                </div>
                            </div>

                            <!-- Satellite 2: Paths (Left) -->
                            <div class="absolute top-1/2 left-0 -translate-y-1/2 -translate-x-4 w-20 h-20 bg-white rounded-3xl shadow-xl flex items-center justify-center border border-purple-50 z-10 animate-[bounce_5s_infinite]">
                                <div class="w-12 h-12 bg-purple-50 rounded-2xl flex items-center justify-center text-purple-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                </div>
                            </div>

                            <!-- Satellite 3: Codes (Right) -->
                            <div class="absolute top-1/2 right-0 -translate-y-1/2 translate-x-4 w-20 h-20 bg-white rounded-3xl shadow-xl flex items-center justify-center border border-teal-50 z-10 animate-[bounce_4.5s_infinite]">
                                <div class="w-12 h-12 bg-teal-50 rounded-2xl flex items-center justify-center text-teal-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4h2v-4zM5 21v-4m4-4H5v4h4v-4zM12 3h.01M12 17h.01M16 3h.01M20 3h.01M16 17h.01M12 13h.01M16 13h.01M20 13h.01M20 17h.01M20 21h.01M12 21h.01M3 21h.01M3 17h.01M7 17h.01M7 21h.01M3 3h4v4H3V3zm14 0h4v4h-4V3zM3 3h4v4H3V3zm0 14h4v4H3v-4z"></path></svg>
                                </div>
                            </div>

                            <!-- Satellite 4: Crews (Bottom) -->
                            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-6 w-20 h-20 bg-white rounded-3xl shadow-xl flex items-center justify-center border border-amber-50 z-10 animate-[bounce_5.5s_infinite]">
                                <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Interactive Feature Section -->
        <section id="features" class="py-24 bg-gray-50 relative" x-data="{ activeFeature: 'files' }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 max-w-2xl mx-auto">
                    <h2 class="text-sm font-bold text-teal-600 tracking-wider uppercase mb-2">Empat Fitur Utama</h2>
                    <h3 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4">Satu Platform untuk Semua</h3>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    
                    <!-- Left: Dynamic Visual -->
                    <div class="relative h-[400px] flex items-center justify-center p-8 transition-all duration-500">
                        
                        <!-- Visual: Files -->
                        <div x-show="activeFeature === 'files'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute inset-0 flex items-center justify-center p-8">
                             <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-blue-100 overflow-hidden">
                                <div class="bg-blue-50 p-4 border-b border-blue-100 flex justify-between items-center">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-600 shadow-sm border border-blue-50">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-blue-900 text-sm">Tugas Matematika</h4>
                                            <p class="text-xs text-blue-500 font-medium">X-IPA 1 • Pak Budi</p>
                                        </div>
                                    </div>
                                    <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2 py-1 rounded-full uppercase tracking-wide">Active</span>
                                </div>
                                
                                <div class="p-6 space-y-4">
                                    <!-- File Item 1 -->
                                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl border border-gray-100 group hover:border-blue-200 transition-colors cursor-pointer">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-red-500 shadow-sm">
                                           <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors">Soal_Ujian.pdf</p>
                                            <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                                                <div class="bg-green-500 h-1.5 rounded-full" style="width: 100%"></div>
                                            </div>
                                        </div>
                                        <div class="text-green-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                    </div>

                                    <!-- File Item 2 -->
                                    <div class="flex items-center gap-3 p-3 bg-blue-50/50 rounded-xl border border-blue-100 group hover:border-blue-300 transition-colors cursor-pointer">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-blue-500 shadow-sm">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-gray-800 group-hover:text-blue-600 transition-colors">Jawaban_Andi.docx</p>
                                            <p class="text-xs text-gray-500">Uploading... 85%</p>
                                        </div>
                                        <div class="relative w-5 h-5">
                                             <svg class="animate-spin w-5 h-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-between items-center">
                                    <div class="flex items-center gap-1 text-xs font-bold text-gray-400">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12.01 1.993C6.486 2.007 2 6.55 2 12.073c0 5.523 4.418 10 9.923 10 5.505 0 9.923-4.477 9.923-10 0-5.522-4.418-10.065-9.836-10.08zM12 20.073c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm1-13h-2v6h2v-6zm0 8h-2v2h2v-2z" fill-opacity="0" stroke="none"/><path d="M7.75 16.5h8.5l-4.25-7.5-4.25 7.5zm.908-1.5l3.342-5.895 3.342 5.895H8.658z" fill="#3B82F6"/></svg>
                                        Synced to Google Drive
                                    </div>
                                    <span class="text-[10px] bg-gray-200 text-gray-500 px-2 py-0.5 rounded">2 Files</span>
                                </div>
                             </div>
                        </div>

                        <!-- Visual: Paths -->
                        <div x-show="activeFeature === 'paths'" style="display: none;" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute inset-0 flex items-center justify-center p-8">
                            <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-purple-100 overflow-hidden">
                                <div class="bg-purple-50 p-4 border-b border-purple-100 flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2.5 h-2.5 rounded-full bg-red-400"></div>
                                        <div class="w-2.5 h-2.5 rounded-full bg-yellow-400"></div>
                                        <div class="w-2.5 h-2.5 rounded-full bg-green-400"></div>
                                    </div>
                                    <span class="text-[10px] font-extrabold text-purple-400 uppercase tracking-widest">Link Manager</span>
                                </div>
                                <div class="p-6 space-y-6">
                                    <!-- Original Link -->
                                    <div class="flex items-center gap-3 text-gray-400 text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        <span class="truncate">https://docs.google.com/presentation/d/1...</span>
                                    </div>
                                    <!-- Arrow Down -->
                                    <div class="flex justify-center -my-3 relative z-10">
                                        <div class="bg-purple-100 text-purple-600 rounded-full p-1.5 ring-4 ring-white">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7"></path></svg>
                                        </div>
                                    </div>
                                    <!-- Short Link (Hero) -->
                                    <div class="bg-gray-50 border-2 border-dashed border-purple-200 rounded-xl p-4 flex items-center justify-between group cursor-pointer hover:border-purple-400 transition-colors">
                                        <div>
                                             <p class="text-[10px] text-gray-400 uppercase font-bold tracking-wider mb-1">Short Link</p>
                                             <p class="text-lg font-bold text-gray-800">q-link.my.id/bio-1</p>
                                        </div>
                                        <div class="text-purple-400 group-hover:text-purple-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </div>
                                    </div>
                                    <!-- Stats -->
                                    <div class="flex items-end justify-between pt-2">
                                        <div>
                                            <p class="text-3xl font-black text-gray-900 tracking-tight">1,245</p>
                                            <p class="text-xs text-gray-500 font-bold">Total Kunjungan</p>
                                        </div>
                                        <div class="flex items-end gap-1 h-10 w-24 pb-1">
                                             <div class="w-1/5 bg-purple-100 h-[40%] rounded-t-sm"></div>
                                             <div class="w-1/5 bg-purple-200 h-[60%] rounded-t-sm"></div>
                                             <div class="w-1/5 bg-purple-300 h-[45%] rounded-t-sm"></div>
                                             <div class="w-1/5 bg-purple-400 h-[80%] rounded-t-sm"></div>
                                             <div class="w-1/5 bg-purple-500 h-[100%] rounded-t-sm shadow-lg shadow-purple-200"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Visual: Codes -->
                        <div x-show="activeFeature === 'codes'" style="display: none;" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute inset-0 flex items-center justify-center p-8">
                             <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl border border-teal-100 p-6 overflow-hidden">
                                <div class="absolute -top-10 -right-10 w-32 h-32 bg-teal-50 rounded-full blur-2xl opacity-50"></div>
                                
                                <h4 class="font-bold text-gray-900 mb-6 flex items-center gap-2 text-lg">
                                    <span class="w-3 h-3 bg-teal-500 rounded-full shadow shadow-teal-200 relative"><span class="absolute inset-0 bg-teal-400 animate-ping opacity-75 rounded-full"></span></span>
                                    QR Customizer
                                </h4>

                                <div class="flex gap-4">
                                    <!-- QR Preview -->
                                    <div class="w-32 h-32 bg-gray-900 rounded-xl flex items-center justify-center relative shadow-lg shrink-0">
                                         <!-- Corner Dots styling -->
                                         <div class="absolute top-2 left-2 w-8 h-8 border-[3px] border-white rounded-md">
                                            <div class="absolute inset-1.5 bg-white rounded-[2px]"></div>
                                         </div>
                                         <div class="absolute top-2 right-2 w-8 h-8 border-[3px] border-white rounded-md"></div>
                                         <div class="absolute bottom-2 left-2 w-8 h-8 border-[3px] border-white rounded-md"></div>
                                         
                                         <!-- Logo Center -->
                                         <div class="absolute inset-0 m-auto w-8 h-8 bg-white rounded-full flex items-center justify-center p-0.5 ring-2 ring-gray-900">
                                             <div class="w-full h-full bg-teal-100 rounded-full flex items-center justify-center text-teal-700 font-bold text-[10px]">Q</div>
                                         </div>
                                    </div>

                                    <!-- Controls -->
                                    <div class="flex-1 space-y-4 pt-1">
                                        <!-- Color Picker -->
                                        <div>
                                            <label class="text-[10px] uppercase font-bold text-gray-400 block mb-2 tracking-wider">Warna Utama</label>
                                            <div class="flex gap-2">
                                                <div class="w-8 h-8 rounded-full bg-gray-900 ring-2 ring-offset-2 ring-gray-200 cursor-pointer"></div>
                                                <div class="w-8 h-8 rounded-full bg-teal-500 cursor-pointer hover:ring-2 hover:ring-offset-2 hover:ring-teal-200 transition-all"></div>
                                                <div class="w-8 h-8 rounded-full bg-indigo-500 cursor-pointer hover:ring-2 hover:ring-offset-2 hover:ring-indigo-200 transition-all"></div>
                                            </div>
                                        </div>
                                        <!-- Logo Toggle -->
                                        <div>
                                             <label class="text-[10px] uppercase font-bold text-gray-400 block mb-2 tracking-wider">Logo Overlay</label>
                                             <div class="w-12 h-6 bg-teal-600 rounded-full relative cursor-pointer px-1 flex items-center">
                                                 <div class="w-4 h-4 bg-white rounded-full ml-auto shadow-sm"></div>
                                             </div>
                                        </div>
                                    </div>
                                </div>

                                <button class="mt-6 w-full py-3 bg-gray-900 text-white rounded-xl font-bold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 hover:bg-black transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    Download PNG
                                </button>
                             </div>
                        </div>
                    <!-- Visual: Crews -->
                    <div x-show="activeFeature === 'crews'" style="display: none;" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute inset-0 flex items-center justify-center p-8">
                         <div class="w-full max-w-sm bg-white rounded-2xl shadow-2xl border border-amber-100 overflow-hidden">
                            <div class="bg-amber-50 p-4 border-b border-amber-100 flex justify-between items-center">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center text-amber-500 shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-amber-900 uppercase tracking-wide">Team Generator</span>
                                </div>
                                <div class="flex -space-x-1">
                                    <div class="w-6 h-6 rounded-full bg-blue-400 border-2 border-white"></div>
                                    <div class="w-6 h-6 rounded-full bg-green-400 border-2 border-white"></div>
                                    <div class="w-6 h-6 rounded-full bg-purple-400 border-2 border-white"></div>
                                </div>
                            </div>
                            
                            <div class="p-6 relative">
                                <!-- Background Elements -->
                                <div class="absolute top-10 right-10 w-20 h-20 bg-amber-50 rounded-full blur-2xl opacity-60 pointer-events-none"></div>

                                <div class="space-y-4">
                                    <!-- Group 1 -->
                                    <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm relative overflow-hidden group hover:border-amber-200 transition-colors">
                                        <div class="absolute top-0 left-0 w-1 h-full bg-amber-400"></div>
                                        <div class="flex justify-between items-center mb-2 pl-3">
                                            <h5 class="font-bold text-amber-900 text-sm">Kelompok 1 (Harimau)</h5>
                                            <span class="text-[10px] bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded font-bold">5 Siswa</span>
                                        </div>
                                        <div class="pl-3 flex flex-wrap gap-1">
                                            <span class="text-[10px] bg-gray-50 text-gray-600 px-2 py-1 rounded border border-gray-100">Budi</span>
                                            <span class="text-[10px] bg-gray-50 text-gray-600 px-2 py-1 rounded border border-gray-100">Siti</span>
                                            <span class="text-[10px] bg-gray-50 text-gray-600 px-2 py-1 rounded border border-gray-100">Ahmad</span>
                                            <span class="text-[10px] text-gray-400 px-1">+2</span>
                                        </div>
                                    </div>

                                    <!-- Group 2 -->
                                    <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm relative overflow-hidden opacity-80">
                                        <div class="absolute top-0 left-0 w-1 h-full bg-blue-400"></div>
                                        <div class="flex justify-between items-center mb-2 pl-3">
                                            <h5 class="font-bold text-gray-700 text-sm">Kelompok 2 (Elang)</h5>
                                            <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-bold">5 Siswa</span>
                                        </div>
                                        <div class="pl-3 flex flex-wrap gap-1">
                                            <span class="text-[10px] bg-gray-50 text-gray-500 px-2 py-1 rounded border border-gray-100">Dewi</span>
                                            <span class="text-[10px] bg-gray-50 text-gray-500 px-2 py-1 rounded border border-gray-100">Rizky</span>
                                            <span class="text-[10px] text-gray-400 px-1">...</span>
                                        </div>
                                    </div>
                                </div>

                                <button class="mt-6 w-full py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-bold text-xs shadow-lg shadow-amber-200 transition-all flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    Acak Ulang Kelompok
                                </button>
                            </div>
                         </div>
                    </div>
                </div>

                <!-- Right: Features List -->
                    <div class="space-y-2">
                        <!-- Item 1 -->
                        <div @click="activeFeature = 'files'" class="cursor-pointer group p-6 rounded-2xl transition-all duration-300 border-l-4" :class="activeFeature === 'files' ? 'bg-white shadow-lg border-blue-500' : 'hover:bg-gray-100 border-transparent'">
                            <h4 class="text-xl font-bold mb-2 transition-colors" :class="activeFeature === 'files' ? 'text-blue-600' : 'text-gray-900'">Files (Drive Integration)</h4>
                            <p class="text-gray-600 leading-relaxed text-sm">
                                Integrasi langsung dengan Google Drive. Guru buat permintaan, siswa upload, semua masuk folder otomatis. Rapih dan tanpa ribet.
                            </p>
                        </div>

                        <!-- Item 2 -->
                        <div @click="activeFeature = 'paths'" class="cursor-pointer group p-6 rounded-2xl transition-all duration-300 border-l-4" :class="activeFeature === 'paths' ? 'bg-white shadow-lg border-purple-500' : 'hover:bg-gray-100 border-transparent'">
                            <h4 class="text-xl font-bold mb-2 transition-colors" :class="activeFeature === 'paths' ? 'text-purple-600' : 'text-gray-900'">Paths (Smart Links)</h4>
                            <p class="text-gray-600 leading-relaxed text-sm">
                                "q-link.my.id/materi-hari-ini" lebih mudah diingat daripada link Google Drive yang panjang. Dilengkapi statistik jumlah klik.
                            </p>
                        </div>

                        <!-- Item 3 -->
                        <div @click="activeFeature = 'codes'" class="cursor-pointer group p-6 rounded-2xl transition-all duration-300 border-l-4" :class="activeFeature === 'codes' ? 'bg-white shadow-lg border-teal-500' : 'hover:bg-gray-100 border-transparent'">
                            <h4 class="text-xl font-bold mb-2 transition-colors" :class="activeFeature === 'codes' ? 'text-teal-600' : 'text-gray-900'">Codes (Custom QR)</h4>
                            <p class="text-gray-600 leading-relaxed text-sm">
                                Ubah link atau teks apapun menjadi QR Code yang cantik. Sesuaikan warna dan tambahkan logo sekolah di tengahnya.
                            </p>
                        </div>

                        <!-- Item 4 (Crews) -->
                        <div @click="activeFeature = 'crews'" class="cursor-pointer group p-6 rounded-2xl transition-all duration-300 border-l-4" :class="activeFeature === 'crews' ? 'bg-white shadow-lg border-amber-500' : 'hover:bg-gray-100 border-transparent'">
                            <h4 class="text-xl font-bold mb-2 transition-colors" :class="activeFeature === 'crews' ? 'text-amber-600' : 'text-gray-900'">Crews (Group Maker)</h4>
                            <p class="text-gray-600 leading-relaxed text-sm">
                                Bagi siswa ke dalam kelompok secara acak dan adil dalam hitungan detik. Cukup masukkan nama, tentukan jumlah, beres!
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white py-12 border-t border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center text-center md:text-left gap-6">
                <div>
                     <div class="flex items-center gap-2 justify-center md:justify-start">
                        <span class="font-bold text-xl text-gray-900 tracking-tight">Q-Space</span>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded font-bold">v2.0.0</span>
                     </div>
                     <p class="text-sm text-gray-500 mt-2 font-medium">Organize your learning universe.</p>
                </div>
                <div class="text-gray-500 text-sm font-medium">
                    &copy; {{ date('Y') }} Q-Space. Hak Cipta Dilindungi.
                </div>
            </div>
        </footer>

        <!-- Toast Notification System -->
        <div 
            x-data="{ 
                notifications: [],
                add(message, type = 'success') {
                    const id = Date.now();
                    this.notifications.push({ id, message, type });
                    setTimeout(() => {
                        this.remove(id);
                    }, 5000);
                },
                remove(id) {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }
            }"
            @notify.window="add($event.detail.message, $event.detail.type)"
            class="fixed top-24 right-4 z-[100] flex flex-col gap-2 pointer-events-none"
        >
            <!-- Flash Messages Listener -->
            @if (session('success'))
                <div x-init="add('{{ session('success') }}', 'success')"></div>
            @endif
            @if (session('error'))
                <div x-init="add('{{ session('error') }}', 'error')"></div>
            @endif

            <template x-for="notification in notifications" :key="notification.id">
                <div 
                    x-show="true"
                    x-transition:enter="transition-all transform ease-out duration-500"
                    x-transition:enter-start="translate-x-full opacity-0"
                    x-transition:enter-end="translate-x-0 opacity-100"
                    x-transition:leave="transition-all transform ease-in duration-300"
                    x-transition:leave-start="translate-x-0 opacity-100"
                    x-transition:leave-end="translate-x-full opacity-0"
                    class="pointer-events-auto transform gpu min-w-[300px] max-w-sm rounded-[1.5rem] p-4 shadow-[0_8px_30px_rgba(0,0,0,0.12)] border backdrop-blur-xl flex items-start gap-3 cursor-pointer"
                    :class="notification.type === 'success' ? 'bg-white/95 border-teal-100 text-teal-800' : 'bg-white/95 border-red-100 text-red-800'"
                    @click="remove(notification.id)"
                >
                    <!-- Icon -->
                    <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center"
                        :class="notification.type === 'success' ? 'bg-teal-50 text-teal-500' : 'bg-red-50 text-red-500'">
                        
                        <template x-if="notification.type === 'success'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </template>
                        
                        <template x-if="notification.type === 'error'">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </template>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 pt-1">
                        <p class="font-bold text-sm" x-text="notification.type === 'success' ? 'Berhasil!' : 'Akses Ditolak'"></p>
                        <p class="text-sm opacity-90 leading-tight mt-1" x-text="notification.message"></p>
                    </div>
                </div>
            </template>
        </div>
    </body>
</html>
