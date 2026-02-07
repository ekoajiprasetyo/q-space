<x-app-layout>
    <x-slot name="title">Files</x-slot>

    <div x-data="{ 
        view: localStorage.getItem('filesView') || 'grid', 
        deleteModalOpen: false,
        deleteUrl: '',
        confirmDelete(url) {
            this.deleteUrl = url;
            this.deleteModalOpen = true;
        }
    }" 
         x-init="$watch('view', value => localStorage.setItem('filesView', value))"
         class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32 relative isolate">

        <!-- Background Decor (Dashboard Style) -->
        <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-blue-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>
        <div class="absolute -top-20 -right-20 w-[500px] h-[500px] bg-purple-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>
        
        <!-- Header & Actions -->
        <div class="flex flex-col xl:flex-row xl:items-center justify-between gap-8 mb-12">
            <div>
                <!-- Title -->
                <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-slate-900 to-slate-700 tracking-tight mb-2">
                    Manajemen File
                </h1>

                <p class="text-slate-500 text-lg font-medium max-w-2xl">
                    Kelola permintaan file yang terintegrasi dengan <span class="text-blue-600 font-bold">Google Drive</span>.
                </p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-center gap-4 w-full xl:w-auto">
                <!-- Google Drive Status -->
                <!-- Google Drive Status -->
                @if(!$googleToken)
                    <a href="{{ route('auth.google.redirect') }}" class="inline-flex justify-center w-full xl:w-auto items-center px-6 py-3 bg-white border border-slate-200 rounded-full font-bold text-sm text-slate-700 hover:bg-slate-50 hover:text-slate-900 transition-all shadow-sm hover:shadow-md gap-3 group">
                        <div class="p-1.5 bg-blue-50 rounded-full group-hover:scale-110 transition-transform">
                            <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12.01 1.989C6.48 1.989 2 6.467 2 11.996c0 5.519 4.47 9.997 10.01 9.997 5.53 0 10-4.478 10-9.997 0-5.529-4.47-10.007-10-10.007Zm0 1.668c2.61 0 4.97 1.01 6.72 2.65L12.01 12 5.29 6.277c1.74-1.63 4.1-2.62 6.72-2.62Zm-8.34 8.339c0-1.89.65-3.64 1.74-5.06L11.53 13H3.69c-.01-.33-.02-.66-.02-1Zm8.34 8.329c-2.47 0-4.73-.91-6.48-2.41L12.01 12l6.47 5.927c-1.74 1.48-4 2.392-6.47 2.392Zm8.34-8.329h-7.85l6.12-5.733c1.09 1.42 1.73 3.17 1.73 5.06 0 .23-.01.45-.03.673Z"/></svg>
                        </div>
                        Hubungkan Drive
                    </a>
                @else
                    <div class="flex items-center justify-between w-full xl:w-auto gap-2 p-1.5 bg-white border border-slate-200 rounded-full shadow-sm pr-4">
                         <span class="inline-flex items-center px-3 py-1.5 bg-blue-50 border border-blue-100 rounded-full font-bold text-xs text-blue-600 gap-2 max-w-[200px] truncate" title="{{ $googleToken->google_email ?? 'Google Drive Terhubung' }}">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                            {{ $googleToken->google_email ?? 'Terhubung' }}
                        </span>
                        <a href="{{ route('auth.google.redirect') }}" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors px-2 whitespace-nowrap">
                            Ganti Akun
                        </a>
                    </div>
                @endif

                <div class="h-8 w-px bg-slate-200 hidden xl:block"></div>

                <!-- Create & Toggle Wrapper -->
                <div class="flex flex-col items-end gap-3 w-full xl:w-auto">
                    <!-- Create Button -->
                    <a href="{{ route('file-requests.create') }}" class="inline-flex justify-center w-full xl:w-auto items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full font-bold text-sm text-white hover:shadow-lg hover:shadow-blue-500/30 hover:scale-105 transition-all duration-300 gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Buat Permintaan
                    </a>
                    
                </div>


                <!-- Desktop View Toggles -->
                <div class="hidden xl:flex bg-white border border-slate-200 p-1 rounded-full items-center shadow-sm">
                    <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-slate-100 text-slate-900 shadow-inner' : 'text-slate-400 hover:text-slate-600'" 
                        class="p-2.5 rounded-full transition-all" title="Tampilan Grid">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-slate-100 text-slate-900 shadow-inner' : 'text-slate-400 hover:text-slate-600'" 
                        class="p-2.5 rounded-full transition-all" title="Tampilan List">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Toggle (Above Search) -->
        <div class="xl:hidden w-full flex justify-end mb-4">
            <div class="bg-white border border-slate-200 p-1 rounded-full flex items-center shadow-sm">
                <button @click="view = 'grid'" 
                    :class="view === 'grid' ? 'bg-slate-100 text-slate-900 shadow-inner' : 'text-slate-400 hover:text-slate-600'" 
                    class="p-2.5 rounded-full transition-all" title="Tampilan Grid">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </button>
                <button @click="view = 'list'" 
                    :class="view === 'list' ? 'bg-slate-100 text-slate-900 shadow-inner' : 'text-slate-400 hover:text-slate-600'" 
                    class="p-2.5 rounded-full transition-all" title="Tampilan List">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
            </div>
        </div>

        <!-- Search & Filter Toolbar -->
        <div class="mb-10 p-2 bg-white rounded-[1.5rem] border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('files.index') }}" class="flex-1 flex flex-col md:flex-row gap-4 w-full">
                <!-- Search Input -->
                <div class="relative flex-1 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" 
                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border-transparent rounded-[1.2rem] text-slate-900 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all font-medium" 
                        placeholder="Cari nama permintaan file...">
                </div>

                <!-- Status Filter -->
                <div class="relative w-full md:w-56 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400 group-focus-within:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </div>
                    <select name="status" onchange="this.form.submit()" 
                        class="block w-full pl-11 pr-10 py-3 bg-slate-50 border-transparent rounded-[1.2rem] text-slate-900 focus:border-blue-500 focus:bg-white focus:ring-0 transition-all font-bold appearance-none cursor-pointer bg-none">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Semua Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif (Arsip)</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </form>
        </div>

        @if($fileRequests->isEmpty())
             <!-- Empty State -->
            <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white/50 p-16 text-center shadow-xl shadow-slate-200/50">
                <div class="w-24 h-24 bg-gradient-to-br from-blue-50 to-indigo-50 text-blue-500 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-inner">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <h3 class="text-2xl font-bold text-slate-900 mb-3">Belum ada permintaan file</h3>
                <p class="text-slate-500 mb-10 max-w-md mx-auto text-lg leading-relaxed">Mulai dengan membuat permintaan file baru untuk mengumpulkan tugas atau dokumen dari siswa Anda.</p>
                <a href="{{ route('file-requests.create') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full font-bold text-white hover:shadow-lg hover:shadow-blue-500/30 hover:-translate-y-1 transition-all duration-300">
                    Buat Permintaan Pertama
                </a>
            </div>
        @else
            <!-- Content Layout -->
            <div :class="view === 'grid' 
                ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8' 
                : 'flex flex-col gap-4'">
                
                @foreach($fileRequests as $request)
                    <div x-data="{ 
                            active: {{ $request->is_active ? 'true' : 'false' }},
                            loading: false,
                            copied: false,
                            toggle() {
                                if (this.loading) return;
                                this.loading = true;
                                fetch('{{ route('file-requests.toggle', $request->id) }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.success) {
                                        this.active = data.is_active;
                                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: data.message, type: 'success' } }));
                                    } else {
                                        window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Gagal mengubah status.', type: 'error' } }));
                                    }
                                })
                                .catch(() => {
                                    window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Terjadi kesalahan jaringan.', type: 'error' } }));
                                })
                                .finally(() => {
                                    this.loading = false;
                                });
                            },
                            copyLink() {
                                navigator.clipboard.writeText('{{ url('/upload/' . $request->slug) }}');
                                this.copied = true;
                                window.dispatchEvent(new CustomEvent('notify', { detail: { message: 'Link berhasil disalin!', type: 'success' } }));
                                setTimeout(() => {
                                    this.copied = false;
                                }, 2000);
                            }
                        }" 
                        class="group transition-all duration-300 relative overflow-hidden"
                        :class="view === 'list' 
                            ? 'bg-white rounded-[1.25rem] border border-slate-200 shadow-sm hover:shadow-md flex items-center p-4 gap-4' 
                            : 'bg-white/80 backdrop-blur-md rounded-[2rem] border border-white/60 shadow-[0_4px_20px_rgb(0,0,0,0.03)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.06)] flex flex-col p-0'">
                        
                        <!-- Card Visual/Icon -->
                        <div :class="view === 'list' 
                            ? 'w-14 h-14 shrink-0 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/20' 
                            : 'h-20 bg-gradient-to-r from-blue-600 to-indigo-700 relative overflow-hidden px-6 py-4'">
                            
                            <!-- Grid View Decor -->
                            <template x-if="view === 'grid'">
                                <div class="w-full h-full relative z-10 flex justify-between items-center">
                                    <!-- Icon -->
                                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md text-white flex items-center justify-center shadow-lg shadow-blue-900/10">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                    </div>

                                    <!-- Actions Wrapper -->
                                    <div class="flex items-center gap-2">
                                        
                                        <!-- Copy Link Button (Only show if active) -->
                                        <button 
                                            x-show="active"
                                            @click="copyLink()"
                                            class="flex items-center justify-center w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white backdrop-blur-md border border-white/10 transition-all hover:scale-110 active:scale-95"
                                           :title="copied ? 'Berhasil Disalin' : 'Salin Link Publik'"
                                        >
                                            <template x-if="!copied">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                            </template>
                                            <template x-if="copied">
                                                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </template>
                                        </button>

                                        <!-- Toggle -->
                                        <div class="flex items-center gap-2 bg-slate-900/20 backdrop-blur-md rounded-full pl-3 pr-1 py-1 border border-white/10 transition-all duration-300"
                                             :class="active ? 'bg-slate-900/20' : 'bg-slate-800/50'">
                                            <span class="text-[10px] font-bold uppercase tracking-widest transition-colors duration-300" 
                                                  :class="active ? 'text-blue-100' : 'text-slate-400'"
                                                  x-text="active ? 'Aktif' : 'Non-Aktif'"></span>
                                            <button @click="toggle()" class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 focus:outline-none shadow-sm" :class="active ? 'bg-blue-500' : 'bg-slate-300'" :disabled="loading">
                                                <span class="sr-only">Ubah</span>
                                                <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-all duration-300 ease-in-out" :class="active ? 'translate-x-6' : 'translate-x-1'"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <!-- List View Icon -->
                            <template x-if="view === 'list'">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                            </template>
                        </div>

                        <!-- Card Body -->
                        <div :class="view === 'list' ? 'flex-1 min-w-0' : 'p-8 flex-1 flex flex-col'">
                             <!-- List View Layout -->
                            <template x-if="view === 'list'">
                                <div>
                                    <h3 class="font-bold text-slate-900 text-base truncate pr-2">
                                        {{ $request->title }}
                                    </h3>
                                    <div class="flex flex-col gap-1 mt-1 text-xs font-medium text-slate-500">
                                         <span class="flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            {{ $request->submissions_count ?? 0 }} Siswa
                                        </span>
                                        <span class="">
                                            {{ $request->deadline ? \Carbon\Carbon::parse($request->deadline)->locale('id')->translatedFormat('l, d M Y • H:i') : 'Tanpa Batas Waktu' }}
                                        </span>
                                    </div>
                                </div>
                            </template>

                            <!-- Grid View Layout (Original) -->
                            <template x-if="view === 'grid'">
                                <div>
                                     <h3 class="font-bold text-slate-900 group-hover:text-blue-600 transition-colors mb-2 text-xl line-clamp-1">
                                        {{ $request->title }}
                                    </h3>
                                    <p class="text-slate-500 text-sm leading-relaxed line-clamp-2 mb-6">
                                        {{ $request->description ?: 'Tidak ada deskripsi tambahan.' }}
                                    </p>
                                    
                                     <div class="border-t border-slate-100 pt-6 mb-8 mt-auto flex items-center gap-4 flex-wrap">
                                        <div class="flex items-center gap-2 bg-blue-50 px-3 py-1.5 rounded-full">
                                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                            <span class="font-bold text-blue-700 text-xs sm:text-sm whitespace-nowrap">
                                                {{ $request->submissions_count ?? 0 }} <span class="ml-0.5">Siswa</span>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-2 bg-slate-50 px-3 py-1.5 rounded-full border border-slate-100">
                                            @if($request->deadline)
                                                <div class="w-2 h-2 rounded-full {{ \Carbon\Carbon::parse($request->deadline)->isPast() ? 'bg-red-500' : 'bg-teal-500' }}"></div>
                                                <span class="font-bold text-slate-600 text-xs sm:text-xs">
                                                    {{ \Carbon\Carbon::parse($request->deadline)->translatedFormat('d M Y, H:i') }}
                                                </span>
                                            @else
                                                <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                                                <span class="font-bold text-slate-400 text-xs sm:text-sm">-</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="mt-auto flex items-center gap-3">
                                        <a href="{{ route('file-requests.show', $request->id) }}" class="flex-1 text-center py-3 rounded-full bg-blue-600 text-white font-bold text-sm hover:bg-blue-700 hover:shadow-lg hover:shadow-blue-500/30 transition-all duration-300">
                                            Kelola File
                                        </a>
                                        <button @click="confirmDelete('{{ route('file-requests.destroy', $request->id) }}')" class="w-11 h-11 rounded-full bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all duration-300 border border-red-100 hover:border-red-500 hover:shadow-lg hover:shadow-red-500/20" title="Hapus Permanen">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- List View Action -->
                        <div x-show="view === 'list'" class="pr-2">
                             <a href="{{ route('file-requests.show', $request->id) }}" class="w-9 h-9 rounded-full bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-blue-600 flex items-center justify-center transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $fileRequests->links() }}
            </div>
        @endif
        
        <!-- Elegant Delete Confirmation Modal -->
        <template x-teleport="body">
            <div x-show="deleteModalOpen" 
                style="display: none;"
                class="fixed inset-0 z-[200] overflow-y-auto" 
                aria-labelledby="modal-title" role="dialog" aria-modal="true">
                
                <!-- Backdrop -->
                <div x-show="deleteModalOpen"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/40 backdrop-blur-[6px] transition-opacity" 
                    @click="deleteModalOpen = false"></div>

                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div x-show="deleteModalOpen"
                        x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave="ease-in duration-200"
                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                        
                        <div class="bg-white px-4 pb-4 pt-5 sm:p-8 sm:pb-6 relative overflow-hidden">
                            <!-- Background decoration -->
                            <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-50 rounded-full blur-2xl"></div>
                            <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-orange-50 rounded-full blur-2xl"></div>

                            <div class="sm:flex sm:items-start relative z-10">
                                <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-red-50 sm:mx-0 sm:h-12 sm:w-12 border border-red-100 shadow-sm ring-4 ring-red-50/50">
                                    <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                    <h3 class="text-xl font-bold leading-6 text-slate-900" id="modal-title">Hapus Permintaan File?</h3>
                                    <div class="mt-3">
                                        <p class="text-slate-500 text-sm leading-relaxed">
                                            Anda yakin ingin menghapus folder ini dan seluruh isinya?
                                        </p>
                                        <div class="mt-4 p-3 bg-red-50/50 rounded-2xl border border-red-100 text-xs text-slate-600 flex gap-3 items-start text-left">
                                            <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>Tindakan ini juga akan <span class="font-bold text-red-600">menghapus folder di Google Drive</span>. Data yang dihapus tidak dapat dikembalikan.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50/50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                            <form :action="deleteUrl" method="POST" class="w-full sm:w-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex w-full justify-center rounded-full bg-red-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-red-500/30 hover:bg-red-500 hover:scale-[1.02] transition-all sm:w-auto">
                                    Ya, Hapus Permanen
                                </button>
                            </form>
                            <button type="button" class="mt-3 inline-flex w-full justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all" @click="deleteModalOpen = false">
                                Batal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
