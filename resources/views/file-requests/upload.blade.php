<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $fileRequest->title }} | Upload File</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Outfit', sans-serif; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800 bg-slate-50 selection:bg-blue-500 selection:text-white relative overflow-x-hidden">
        
        <!-- Background Decor (Create Style) -->
        <div class="fixed top-0 left-[-10%] w-[500px] h-[500px] bg-blue-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>
        <div class="fixed bottom-0 right-[-10%] w-[500px] h-[500px] bg-purple-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>

        <div class="min-h-screen flex items-center justify-center p-4 md:p-8">
            <!-- Main Container -->
            <div class="relative w-full max-w-6xl mx-auto bg-white/60 backdrop-blur-2xl rounded-[3rem] shadow-[0_8px_40px_rgba(0,0,0,0.04)] overflow-hidden border border-white/60 flex flex-col md:flex-row min-h-[600px] z-10" x-data="{ dragging: false }">
                
                <!-- Left Panel (Visual & Info) -->
                <div class="w-full md:w-5/12 bg-gradient-to-br from-slate-900 to-slate-800 p-10 md:p-14 text-white flex flex-col justify-between relative overflow-hidden group">
                    <!-- Decorative Circle -->
                    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-blue-500/30 rounded-full blur-3xl group-hover:bg-blue-500/40 transition-all duration-700"></div>
                    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-purple-500/30 rounded-full blur-3xl group-hover:bg-purple-500/40 transition-all duration-700"></div>
                    
                    <div class="relative z-10">
                        <!-- Header Badge -->
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-blue-200 text-xs font-bold uppercase tracking-wider mb-6 border border-white/10">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
                            Permintaan File
                        </div>

                        <h1 class="text-3xl md:text-5xl font-black tracking-tight leading-tight mb-6 break-words">
                            {{ $fileRequest->title }}
                        </h1>
                        
                        @if($fileRequest->description)
                            <p class="text-slate-400 font-medium leading-relaxed text-lg mb-8">
                                {{ $fileRequest->description }}
                            </p>
                        @else
                            <p class="text-slate-500 font-medium leading-relaxed italic mb-8">
                                Tidak ada instruksi tambahan.
                            </p>
                        @endif

                        <!-- Meta Info Cards -->
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Deadline -->
                            @if($fileRequest->deadline)
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                <div class="w-10 h-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Batas Waktu</p>
                                    <p class="font-bold {{ \Carbon\Carbon::parse($fileRequest->deadline)->isPast() ? 'text-red-400' : 'text-white' }}">
                                        {{ \Carbon\Carbon::parse($fileRequest->deadline)->translatedFormat('l, d M Y - H:i') }}
                                    </p>
                                </div>
                            </div>
                            @endif

                             <!-- Constraints -->
                             <div class="flex gap-4">
                                <div class="flex-1 flex items-center gap-3 p-3 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-sm">
                                    <div class="w-8 h-8 rounded-lg bg-purple-500/20 text-purple-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase">Maks File</p>
                                        <p class="font-bold text-sm text-white">{{ $fileRequest->max_files }} File</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bottom Branding -->
                    <div class="relative z-10 mt-10 md:mt-0 flex items-center gap-3">
                         <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-teal-400 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-teal-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="none" class="w-5 h-5">
                                <circle cx="50" cy="50" r="15" fill="currentColor" />
                                <ellipse cx="50" cy="50" rx="35" ry="10" stroke="currentColor" stroke-width="8" transform="rotate(45 50 50)" stroke-opacity="0.8"/>
                                <ellipse cx="50" cy="50" rx="35" ry="10" stroke="currentColor" stroke-width="8" transform="rotate(-45 50 50)" stroke-opacity="0.8"/>
                            </svg>
                        </div>
                        <span class="text-sm font-bold tracking-wide opacity-80">Q-Space</span>
                    </div>
                </div>

                <!-- Right Panel (Form) -->
                <div class="w-full md:w-7/12 p-8 md:p-14 bg-transparent font-sans">
                    <style>
                        /* Custom Autofill Styling to remove background */
                        input:-webkit-autofill,
                        input:-webkit-autofill:hover, 
                        input:-webkit-autofill:focus, 
                        input:-webkit-autofill:active {
                            -webkit-box-shadow: 0 0 0 30px #f8fafc inset !important;
                            -webkit-text-fill-color: #1e293b !important;
                            transition: background-color 5000s ease-in-out 0s;
                        }
                    </style>

                    @if(session('submission_details'))
                        <!-- Success State View -->
                        <div class="h-full flex flex-col items-center justify-center text-center animate-fade-in-up py-8"
                            x-data="{
                                queueRunnerUrl: @js(session('submission_details')['runner_url'] ?? null),
                                async triggerQueueRunner() {
                                    if (!this.queueRunnerUrl) return;
                                    try {
                                        await fetch(this.queueRunnerUrl, { method: 'GET', keepalive: true, credentials: 'same-origin' });
                                    } catch (_) {
                                        // Silent fail: runner bisa dicoba lagi saat user reload
                                    }
                                }
                            }"
                            x-init="setTimeout(() => triggerQueueRunner(), 700)">
                            <div class="w-24 h-24 rounded-3xl bg-teal-50 text-teal-500 flex items-center justify-center mb-6 shadow-xl shadow-teal-500/10">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            
                            <h2 class="text-3xl font-black text-slate-800 mb-2">
                                {{ (session('submission_details')['is_queued'] ?? false) ? 'Upload Diterima!' : 'Berhasil Terkirim!' }}
                            </h2>
                            <p class="text-slate-500 font-medium mb-10 max-w-sm">
                                {{ (session('submission_details')['is_queued'] ?? false) ? 'File sudah diterima server dan sedang diproses ke Google Drive. Mohon tunggu beberapa saat.' : 'Tugas Anda telah berhasil diupload ke sistem kami.' }}
                            </p>

                            <div class="w-full bg-slate-50 rounded-3xl p-8 mb-8 border border-slate-100/50 text-left">
                                <div class="grid grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Nama</p>
                                        <p class="font-bold text-slate-800 text-lg break-words">{{ session('submission_details')['name'] }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Kelas</p>
                                        <p class="font-bold text-slate-800 text-lg">{{ session('submission_details')['class'] }}</p>
                                    </div>
                                </div>

                                @if(session('submission_details')['notes'])
                                <div class="mb-6">
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Catatan</p>
                                    <p class="text-slate-700 font-medium leading-relaxed break-words">{{ session('submission_details')['notes'] }}</p>
                                </div>
                                @endif

                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">
                                        {{ (session('submission_details')['is_queued'] ?? false) ? 'File Dalam Proses' : 'File Terupload' }}
                                        ({{ count(session('submission_details')['files']) }})
                                    </p>
                                    <div class="space-y-2">
                                        @foreach(session('submission_details')['files'] as $fileName)
                                            <div class="flex items-center gap-3 p-3 rounded-xl bg-white border border-slate-200/50">
                                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                </div>
                                                <span class="text-sm font-bold text-slate-700 truncate">{{ $fileName }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div x-data="{ isReloading: false }">
                                <a href="{{ route('file-requests.upload', $fileRequest->slug) }}" 
                                @click="if(!isReloading) { isReloading = true; }"
                                class="inline-flex items-center gap-2 px-8 py-4 bg-slate-900 text-white rounded-full font-bold hover:bg-slate-800 transition-all hover:scale-105 active:scale-95 shadow-xl hover:shadow-2xl hover:shadow-slate-900/20"
                                :class="isReloading ? 'opacity-75 scale-100 cursor-wait' : ''">
                                    
                                    <template x-if="!isReloading">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    </template>

                                    <template x-if="isReloading">
                                        <svg class="animate-spin -ml-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </template>
                                    
                                    <span x-text="isReloading ? 'Memproses...' : 'Kirim File Lagi'"></span>
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Flash Messages -->
                        @if(session('error'))
                            <div class="mb-8 p-4 rounded-3xl bg-red-50 border border-red-100 text-red-700 flex items-center gap-4 shadow-sm">
                                <div class="w-10 h-10 rounded-2xl bg-red-100 flex items-center justify-center shrink-0 text-red-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </div>
                                <div>
                                    <p class="font-bold text-lg">Terjadi Kesalahan</p>
                                    <p class="text-sm opacity-90">{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- Validation Errors ($errors bag dari Laravel) --}}
                        @if($errors->any())
                            <div class="mb-8 p-4 rounded-3xl bg-red-50 border border-red-100 text-red-700 shadow-sm">
                                <div class="flex items-center gap-3 mb-3">
                                    <div class="w-10 h-10 rounded-2xl bg-red-100 flex items-center justify-center shrink-0 text-red-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    </div>
                                    <p class="font-bold text-lg">Formulir Tidak Lengkap</p>
                                </div>
                                <ul class="list-disc list-inside text-sm space-y-1 opacity-90 pl-1">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                @if($errors->has('files') || $errors->has('files.*'))
                                    <p class="text-xs mt-3 font-medium text-red-600 bg-red-100 rounded-2xl px-3 py-2">
                                        💡 Tip: Pastikan file sudah dipilih sebelum klik Kirim. Jika file terlalu besar, coba kompres terlebih dahulu.
                                    </p>
                                @endif
                            </div>
                        @endif

                        <form id="upload-form" action="{{ route('file-requests.upload.store', $fileRequest->slug) }}" method="POST" enctype="multipart/form-data" class="h-full flex flex-col space-y-8" 
                            x-data="{ 
                                isUploading: false, 
                                dragging: false,
                                files: [], 
                                maxFiles: {{ $fileRequest->max_files }},
                                notifications: [],
                                uploadProgress: 0,
                                uploadStatusText: 'Mempersiapkan upload...',
                                _progressTimer: null,
                                
                                addNotification(message, type = 'error') {
                                    const id = Date.now();
                                    this.notifications.push({ id, message, type });
                                    setTimeout(() => {
                                        this.notifications = this.notifications.filter(n => n.id !== id);
                                    }, 3000);
                                },

                                updateInputFiles(fileList) {
                                    const dt = new DataTransfer();
                                    fileList.forEach(file => dt.items.add(file));
                                    const input = document.getElementById('file-upload');
                                    input.files = dt.files;
                                    this.files = Array.from(dt.files);
                                },

                                handleFiles(event) {
                                    const input = event.target;
                                    const newFiles = Array.from(input.files);
                                    this.processFiles(newFiles);
                                },

                                handleDrop(event) {
                                    const newFiles = Array.from(event.dataTransfer.files);
                                    this.processFiles(newFiles);
                                },

                                processFiles(newFiles) {
                                    const currentFiles = this.files;
                                    const allFiles = [...currentFiles, ...newFiles];
                                    
                                    const uniqueMap = new Map();
                                    allFiles.forEach(f => {
                                        const key = f.name + '|' + f.size;
                                        if (!uniqueMap.has(key)) uniqueMap.set(key, f);
                                    });
                                    
                                    const finalFiles = Array.from(uniqueMap.values());

                                    if (finalFiles.length > this.maxFiles) {
                                        this.addNotification(`Maksimal ${this.maxFiles} file yang diperbolehkan.`, 'error');
                                        this.updateInputFiles(currentFiles);
                                        return;
                                    }

                                    this.updateInputFiles(finalFiles);
                                },

                                removeFile(index) {
                                    const currentFiles = this.files;
                                    const newFiles = currentFiles.filter((_, i) => i !== index);
                                    this.updateInputFiles(newFiles);
                                },

                                startSimulatedProgress() {
                                    // Hitung total ukuran file untuk estimasi durasi
                                    const totalBytes = this.files.reduce((sum, f) => sum + f.size, 0);
                                    const totalMB = totalBytes / 1024 / 1024;
                                    const totalFiles = this.files.length;

                                    this.uploadProgress = 0;
                                    this.uploadStatusText = `Mempersiapkan ${totalFiles} file (${totalMB.toFixed(1)} MB)...`;

                                    const self = this;
                                    // Progress naik cepat 0→30% (fase upload ke server)
                                    // lalu lambat 30→88% (fase server proses ke Drive)
                                    // lalu berhenti di 88% menunggu redirect dari server
                                    let pct = 0;

                                    function tick() {
                                        if (pct < 30) {
                                            pct += 2.5;
                                            self.uploadStatusText = `Mengupload ke server... ${pct.toFixed(0)}%`;
                                        } else if (pct < 88) {
                                            pct += 0.6;
                                            self.uploadStatusText = `Menyimpan ke Google Drive...`;
                                        } else {
                                            // Berhenti di 88, tunggu server selesai
                                            self.uploadStatusText = 'Menyelesaikan proses upload...';
                                            return;
                                        }
                                        self.uploadProgress = Math.min(Math.round(pct), 88);
                                        self._progressTimer = setTimeout(tick, 120);
                                    }

                                    tick();
                                }
                            }" 
                            @submit="isUploading = true; startSimulatedProgress();">
                            @csrf
                            
                            <!-- Toast Notifications -->
                            <template x-teleport="body">
                                <div class="fixed top-4 right-4 z-50 flex flex-col gap-2 w-full max-w-sm pointer-events-none">
                                    <template x-for="notification in notifications" :key="notification.id">
                                        <div 
                                            x-transition:enter="transition ease-out duration-300"
                                            x-transition:enter-start="opacity-0 translate-x-8"
                                            x-transition:enter-end="opacity-100 translate-x-0"
                                            x-transition:leave="transition ease-in duration-200"
                                            x-transition:leave-start="opacity-100 translate-x-0"
                                            x-transition:leave-end="opacity-0 translate-x-8"
                                            class="pointer-events-auto flex items-center gap-3 p-4 rounded-2xl shadow-xl border backdrop-blur-md"
                                            :class="notification.type === 'error' ? 'bg-red-500/90 text-white border-red-400/50' : 'bg-slate-800/90 text-white border-slate-700/50'"
                                        >
                                            <div class="shrink-0">
                                                <template x-if="notification.type === 'error'">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                </template>
                                                <template x-if="notification.type !== 'error'">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </template>
                                            </div>
                                            <p class="font-bold text-sm" x-text="notification.message"></p>
                                            <button type="button" @click="notifications = notifications.filter(n => n.id !== notification.id)" class="ml-auto opacity-70 hover:opacity-100 transition-opacity">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Custom Input: Name -->
                                <div class="group">
                                    <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-blue-600 transition-colors">Nama Lengkap</label>
                                    <div class="relative">
                                         <input 
                                            id="name" 
                                            type="text" 
                                            name="name" 
                                            value="{{ old('name') }}"
                                            required 
                                            placeholder="Nama Anda" 
                                            class="block w-full border-0 border-b border-slate-200 bg-transparent py-3 px-0 text-lg font-medium text-slate-800 focus:border-slate-300 focus:ring-0 focus:outline-none placeholder-slate-300 transition-colors font-sans"
                                        />
                                        <div class="absolute right-0 top-1/2 -translate-y-1/2 opacity-0 group-focus-within:opacity-100 transition-opacity text-blue-500">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom Input: Class -->
                                <div class="group">
                                    <label for="class_name" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-blue-600 transition-colors">Kelas</label>
                                    <div class="relative">
                                         <input 
                                            id="class_name" 
                                            type="text" 
                                            name="class_name" 
                                            value="{{ old('class_name') }}"
                                            required 
                                            placeholder="Contoh: XII-1" 
                                            class="block w-full border-0 border-b border-slate-200 bg-transparent py-3 px-0 text-lg font-medium text-slate-800 focus:border-slate-300 focus:ring-0 focus:outline-none placeholder-slate-300 transition-colors font-sans"
                                        />
                                        <div class="absolute right-0 top-1/2 -translate-y-1/2 opacity-0 group-focus-within:opacity-100 transition-opacity text-blue-500">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Input: Notes -->
                            <div class="group">
                                <label for="notes" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-blue-600 transition-colors">Catatan <span class="text-slate-300 font-normal normal-case ml-1">(Opsional)</span></label>
                                <div class="relative bg-slate-50 rounded-3xl p-4 transition-all focus-within:ring-0 focus-within:bg-white border-2 border-transparent focus-within:border-slate-200">
                                     <textarea 
                                        id="notes" 
                                        name="notes" 
                                        rows="3" 
                                        class="block w-full border-0 bg-transparent p-0 text-slate-800 placeholder-slate-400 focus:ring-0 resize-none font-medium leading-relaxed"
                                        placeholder="Tuliskan pesan tambahan untuk guru..."
                                    >{{ old('notes') }}</textarea>
                                </div>
                            </div>

                            <!-- Dropzone styled as Create's File Input -->
                            <div class="group">
                                <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-blue-600 transition-colors">File Tugas</label>
                                <div 
                                    class="relative w-full border-3 border-dashed rounded-3xl min-h-[350px] flex flex-col transition-all duration-300 ease-out bg-slate-50/50 hover:bg-white group-hover:border-blue-400"
                                    :class="dragging ? 'border-blue-500 bg-blue-50/50 scale-[1.02]' : 'border-slate-300'"
                                    @dragover.prevent="dragging = true"
                                    @dragleave.prevent="dragging = false"
                                    @drop.prevent="handleDrop($event); dragging = false"
                                >
                                    <input type="file" id="file-upload" name="files[]" multiple class="hidden" required @change="handleFiles($event)">
                                    
                                    <!-- Empty State -->
                                    <div 
                                        class="flex-1 flex flex-col items-center justify-center gap-4 p-8 md:p-10 cursor-pointer text-center"
                                        x-show="files.length === 0"
                                        onclick="document.getElementById('file-upload').click()"
                                    >
                                        <div class="w-16 h-16 rounded-2xl bg-white shadow-lg shadow-blue-900/5 flex items-center justify-center text-blue-500 mb-2 transition-transform duration-300 group-hover:scale-110 group-hover:-rotate-3">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-xl font-bold text-slate-700 font-sans">Klik untuk unggah file</p>
                                            <p class="text-slate-400 font-medium mt-1">atau tarik & lepas di sini</p>
                                        </div>
                                    </div>
                                    
                                    <!-- File List State -->
                                    <div x-show="files.length > 0" class="flex-1 flex flex-col p-6 w-full" style="display: none;">
                                        <div class="flex justify-between items-center mb-6 pb-4 border-b border-slate-100">
                                            <div class="flex items-center gap-3">
                                                <div class="px-3 py-1 rounded-full bg-blue-100 text-blue-600 text-xs font-bold uppercase tracking-wider">
                                                    <span x-text="files.length"></span> File
                                                </div>
                                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Terpilih</span>
                                            </div>
                                            <button type="button" @click="document.getElementById('file-upload').click()" class="flex items-center gap-2 text-xs font-bold text-blue-500 hover:text-blue-700 uppercase tracking-wider transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                                Tambah / Ubah
                                            </button>
                                        </div>

                                        <div class="grid grid-cols-1 gap-3 overflow-y-auto max-h-[400px] pr-2 custom-scrollbar">
                                            <template x-for="(file, index) in files" :key="index">
                                                <div class="group/file relative flex items-center gap-4 p-4 rounded-2xl bg-white border border-slate-100 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-500/5 transition-all duration-300">
                                                    <!-- Icon & Type -->
                                                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-teal-50 to-teal-100 text-teal-600 flex items-center justify-center shrink-0 border border-teal-200/50">
                                                        <template x-if="file.type.includes('image')">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        </template>
                                                        <template x-if="file.type.includes('pdf')">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                                        </template>
                                                        <template x-if="!file.type.includes('image') && !file.type.includes('pdf')">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                        </template>
                                                    </div>
                                                    
                                                    <!-- Info -->
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-bold text-slate-700 truncate font-sans" x-text="file.name"></p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <span class="text-[10px] font-bold uppercase bg-slate-100 text-slate-400 px-2 py-0.5 rounded" x-text="(file.size / 1024 / 1024).toFixed(1) + ' MB'"></span>
                                                            <span class="text-[10px] font-bold uppercase text-slate-300" x-text="file.type.split('/')[1] || 'FILE'"></span>
                                                        </div>
                                                    </div>

                                                    <!-- Delete Action -->
                                                    <button type="button" @click="removeFile(index)" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-300 hover:text-red-500 hover:bg-red-50 transition-all opacity-0 group-hover/file:opacity-100 focus:opacity-100">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-end">
                                <button type="submit" 
                                    class="group relative w-full md:w-auto px-10 py-4 bg-slate-900 text-white rounded-full font-bold overflow-hidden shadow-xl hover:shadow-2xl hover:shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-70 disabled:pointer-events-none"
                                    :disabled="isUploading || files.length === 0">
                                    <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    
                                    <!-- Normal State -->
                                    <div class="relative flex items-center justify-center gap-3" x-show="!isUploading">
                                        <span>Kirim Tugas Saya</span>
                                        <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </div>

                                    <!-- Loading State -->
                                    <div class="relative flex items-center justify-center gap-3" x-show="isUploading" style="display: none;">
                                        <svg class="animate-spin -ml-1 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        <span>Mengupload...</span>
                                    </div>
                                </button>
                            </div>

                            <!-- Full-Screen Upload Progress Overlay -->
                            <template x-teleport="body">
                                <div x-show="isUploading"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100"
                                    x-transition:leave-end="opacity-0"
                                    class="fixed inset-0 z-[9999] flex items-center justify-center p-6"
                                    style="display: none;">
                                    <!-- Backdrop blur -->
                                    <div class="absolute inset-0 bg-slate-900/70 backdrop-blur-md"></div>

                                    <!-- Card -->
                                    <div class="relative z-10 w-full max-w-md bg-white rounded-[2rem] shadow-2xl p-10 flex flex-col items-center text-center">
                                        <!-- Animated Icon -->
                                        <div class="relative w-24 h-24 mb-8">
                                            <!-- Outer ring -->
                                            <svg class="absolute inset-0 w-full h-full -rotate-90" viewBox="0 0 100 100">
                                                <circle cx="50" cy="50" r="42" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                                                <circle cx="50" cy="50" r="42" fill="none" stroke="url(#progressGradient)" stroke-width="8"
                                                    stroke-linecap="round"
                                                    :stroke-dasharray="`${2 * Math.PI * 42}`"
                                                    :stroke-dashoffset="`${2 * Math.PI * 42 * (1 - uploadProgress / 100)}`"
                                                    style="transition: stroke-dashoffset 0.4s ease;"
                                                />
                                                <defs>
                                                    <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                                        <stop offset="0%" stop-color="#3b82f6"/>
                                                        <stop offset="100%" stop-color="#6366f1"/>
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                            <!-- Inner icon -->
                                            <div class="absolute inset-0 flex items-center justify-center">
                                                <template x-if="uploadProgress < 100">
                                                    <svg class="w-9 h-9 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                    </svg>
                                                </template>
                                                <template x-if="uploadProgress >= 100">
                                                    <svg class="w-9 h-9 text-indigo-500 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                    </svg>
                                                </template>
                                            </div>
                                        </div>

                                        <!-- Percentage -->
                                        <div class="mb-2">
                                            <span class="text-5xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent" x-text="uploadProgress + '%'"></span>
                                        </div>

                                        <!-- Status Text -->
                                        <p class="text-slate-500 font-medium text-sm mb-8 min-h-[40px]" x-text="uploadStatusText"></p>

                                        <!-- Progress Bar -->
                                        <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-500 transition-all duration-500 ease-out relative overflow-hidden"
                                                :style="'width: ' + uploadProgress + '%'">
                                                <!-- Shimmer effect -->
                                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                            </div>
                                        </div>

                                        <!-- File counter -->
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-4">
                                            <span x-text="files.length"></span> file sedang diupload ke Google Drive
                                        </p>
                                    </div>
                                </div>
                            </template>
                        </form>
                    @endif
                </div>
            </div>
            

        </div>
    </body>
</html>
