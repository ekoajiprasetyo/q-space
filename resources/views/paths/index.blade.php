<x-app-layout>
    <x-slot name="title">Paths Management</x-slot>

    <div x-data="{ 
        deleteModalOpen: false,
        deleteUrl: '',
        editModalOpen: false,
        editActionUrl: '',
        oldOriginalUrl: '',
        oldShortCode: '',
        isSubmitting: false,
        confirmDelete(url) {
            this.deleteUrl = url;
            this.deleteModalOpen = true;
        },
        openEditModal(actionUrl, originalUrl, shortCode) {
            this.editActionUrl = actionUrl;
            this.oldOriginalUrl = originalUrl;
            this.oldShortCode = shortCode;
            this.editModalOpen = true;
        }
    }" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 relative isolate">
        
        <!-- Background Decoration -->
        <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-blue-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>
        <div class="absolute -top-20 -right-20 w-[500px] h-[500px] bg-purple-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>

        <div class="flex flex-col gap-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-slate-900 to-slate-700 tracking-tight mb-2">
                        Manajemen Paths
                    </h1>
                    <p class="text-lg text-slate-500 font-medium max-w-2xl">
                        Buat dan kelola tautan pendek untuk memudahkan akses materi pembelajaran.
                    </p>
                </div>
                
                <!-- Right Side: Search & Stats -->
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <!-- Search Input -->
                    <form method="GET" action="{{ route('paths.index') }}" class="relative w-full sm:w-64">
                         <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari path..." 
                            class="block w-full pl-10 pr-4 py-2.5 bg-white/80 backdrop-blur-md border border-white/50 rounded-full text-sm font-bold text-slate-700 placeholder-slate-400 focus:border-purple-500 focus:ring-purple-500 shadow-sm transition-all">
                    </form>

                     <!-- Stats Capsule -->
                    <div class="inline-flex items-center gap-3 px-5 py-2.5 bg-white/80 backdrop-blur-md rounded-full shadow-sm border border-white/50">
                        <div class="flex items-center gap-2">
                            <div class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></div>
                            <span class="text-sm font-bold text-slate-600">Total Paths</span>
                        </div>
                        <div class="h-4 w-px bg-slate-200"></div>
                        <span class="text-lg font-black text-slate-800">{{ $links->count() }}</span>
                    </div>
                </div>
            </div>



            <!-- Create New Path Card -->
            <div class="bg-white/80 backdrop-blur-xl rounded-[2rem] border border-white/50 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden relative group">
                <!-- Decorative Top Border -->
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500 opacity-50"></div>
                
                <div class="p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-purple-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Buat Path Baru</h3>
                            <p class="text-slate-500 text-sm">Tempel URL panjang Anda dan buat alias kustom.</p>
                        </div>
                    </div>

                    <form action="{{ route('paths.store') }}" method="POST" class="flex flex-col lg:flex-row gap-4"
                        @submit="
                            let urlInput = document.getElementById('original_url');
                            if(urlInput.value && !urlInput.value.match(/^https?:\/\//)) { urlInput.value = 'https://' + urlInput.value; }
                            isSubmitting = true;
                        ">
                        @csrf
                        
                        <div class="w-full lg:w-64 group/input">
                            <label for="name" class="block text-sm font-bold text-slate-700 mb-2 ml-1">Nama Link</label>
                            <input type="text" name="name" id="name" placeholder="Mis: Materi Web" 
                                class="block w-full px-4 py-3.5 bg-slate-50 border-transparent rounded-2xl text-slate-900 placeholder-slate-400 focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 transition-all font-medium">
                        </div>

                        <div class="flex-grow group/input">
                            <label for="original_url" class="block text-sm font-bold text-slate-700 mb-2 ml-1">URL Asli</label>
                            <div class="flex shadow-sm rounded-2xl overflow-hidden">
                                <span class="inline-flex items-center px-4 bg-slate-100 border-r-0 border-transparent text-slate-500 text-sm font-bold">https://</span>
                                <input type="text" name="original_url" id="original_url" placeholder="google.com" 
                                    class="flex-1 min-w-0 block w-full px-4 py-3.5 bg-slate-50 border-transparent text-slate-900 placeholder-slate-400 focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 transition-all font-medium" required>
                            </div>
                            @error('original_url') <span class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="w-full lg:w-72 group/input">
                            <label for="short_code" class="block text-sm font-bold text-slate-700 mb-2 ml-1">Alias Kustom (Opsional)</label>
                            <div class="flex shadow-sm rounded-2xl overflow-hidden">
                                <span class="inline-flex items-center px-4 bg-slate-100 border-r-0 border-transparent text-slate-500 text-sm font-bold">/</span>
                                <input type="text" name="short_code" id="short_code" placeholder="my-link" 
                                    class="flex-1 min-w-0 block w-full px-4 py-3.5 bg-slate-50 border-transparent text-slate-900 placeholder-slate-400 focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 transition-all font-medium">
                            </div>
                            @error('short_code') <span class="text-red-500 text-xs font-bold mt-1 ml-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex flex-col justify-end">
                            <button type="submit"
                                :disabled="isSubmitting"
                                class="h-[52px] inline-flex items-center justify-center gap-2 px-8 bg-gradient-to-r from-purple-600 to-indigo-600 rounded-full font-bold text-white hover:shadow-lg hover:shadow-purple-500/30 hover:scale-105 active:scale-95 transition-all duration-300 disabled:opacity-75 disabled:pointer-events-none disabled:scale-100">

                                <!-- Normal State -->
                                <template x-if="!isSubmitting">
                                    <span class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        Singkat &amp; Simpan
                                    </span>
                                </template>

                                <!-- Loading State -->
                                <template x-if="isSubmitting">
                                    <span class="flex items-center gap-2">
                                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Memproses...
                                    </span>
                                </template>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Paths -->
            <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white/50 shadow-sm overflow-hidden">
                <div class="p-8 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-3">
                        <span class="w-2 h-8 rounded-full bg-purple-500"></span>
                        Daftar Paths
                    </h3>
                </div>
                
                @if($links->isEmpty())
                     <div class="p-20 text-center">
                        <div class="w-24 h-24 bg-purple-50 text-purple-300 rounded-3xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Belum ada path dibuat</h3>
                        <p class="text-slate-500 max-w-sm mx-auto">Path yang Anda buat akan muncul di sini lengkap dengan statistik pengunjungnya.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 text-slate-400 text-xs uppercase tracking-wider font-bold">
                                    <th class="px-6 py-6 w-1/5">Nama Link</th>
                                    <th class="px-6 py-6 w-1/6">Short Link</th>
                                    <th class="px-6 py-6 w-1/3">URL Asli</th>
                                    <th class="px-6 py-6 text-center w-24">Kunjungan</th>
                                    <th class="px-6 py-6 w-32">Dibuat</th>
                                    <th class="px-6 py-6 text-right w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($links as $link)
                                    <tr class="group hover:bg-purple-50/30 transition-colors">
                                        <td class="px-8 py-5">
                                            <span class="font-bold text-slate-800 block truncate max-w-[150px]" title="{{ $link->name }}">{{ $link->name ?? '-' }}</span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center font-bold text-lg shrink-0">
                                                    /
                                                </div>
                                                <div class="flex flex-col">
                                                    <a href="https://s.q-link.my.id/{{ $link->short_code }}" target="_blank" class="font-bold text-purple-700 hover:text-purple-900 hover:underline text-base">
                                                        {{ $link->short_code }}
                                                    </a>
                                                    <span class="text-xs text-slate-400 font-medium">s.q-link.my.id</span>
                                                </div>
                                                <button onclick="navigator.clipboard.writeText('https://s.q-link.my.id/{{ $link->short_code }}'); this.innerHTML = '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\'></path></svg>'; setTimeout(() => this.innerHTML = '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\'></path></svg>', 2000)" 
                                                    class="p-2 rounded-lg text-slate-400 hover:text-purple-600 hover:bg-purple-100 transition-all" title="Copy">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex items-center gap-2 max-w-[250px]">
                                                <img src="https://www.google.com/s2/favicons?domain={{ parse_url($link->original_url, PHP_URL_HOST) }}&sz=32" alt="" class="w-4 h-4 opacity-60 flex-shrink-0">
                                                <a href="{{ $link->original_url }}" target="_blank" class="text-slate-500 font-medium truncate hover:text-blue-600 transition-colors block w-full" title="{{ $link->original_url }}">
                                                    {{ $link->original_url }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-center">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200 group-hover:bg-white group-hover:shadow-sm transition-all">
                                                {{ number_format($link->visits) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-5">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-slate-700">{{ $link->created_at->format('d M Y') }}</span>
                                                <span class="text-xs text-slate-400">{{ $link->created_at->format('H:i') }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-5 text-right">
                                            <button @click="openEditModal('{{ route('paths.update', $link) }}', '{{ $link->original_url }}', '{{ $link->short_code }}')" 
                                                class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all duration-300 mr-2" title="Edit Path">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            </button>
                                            <button @click="confirmDelete('{{ route('paths.destroy', $link) }}')" 
                                                class="w-9 h-9 rounded-full bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all duration-300" title="Hapus">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($links->hasPages())
                        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                            {{ $links->appends(request()->query())->links() }}
                        </div>
                    @endif
                @endif
            </div>
            <!-- Delete Modal -->
            <template x-teleport="body">
                <div x-show="deleteModalOpen" class="relative z-[999]" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                    <div x-show="deleteModalOpen" 
                        x-transition:enter="ease-out duration-300" 
                        x-transition:enter-start="opacity-0" 
                        x-transition:enter-end="opacity-100" 
                        x-transition:leave="ease-in duration-200" 
                        x-transition:leave-start="opacity-100" 
                        x-transition:leave-end="opacity-0" 
                        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity">
                    </div>

                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <!-- Modal Panel -->
                            <div x-show="deleteModalOpen" 
                                @click.away="deleteModalOpen = false" 
                                x-transition:enter="ease-out duration-300" 
                                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                                x-transition:leave="ease-in duration-200" 
                                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                                class="relative transform overflow-hidden rounded-[2rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                                
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
                                            <h3 class="text-xl font-bold leading-6 text-slate-900" id="modal-title">Hapus Path?</h3>
                                            <div class="mt-3">
                                                <p class="text-slate-500 text-sm leading-relaxed">
                                                    Anda yakin ingin menghapus path ini?
                                                </p>
                                                <div class="mt-4 p-3 bg-red-50/50 rounded-2xl border border-red-100 text-xs text-slate-600 flex gap-3 items-start text-left">
                                                    <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    <p>Path yang dihapus tidak lagidapat diakses. Data kunjungan juga akan <span class="font-bold text-red-600">terhapus permanen</span>.</p>
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
                                            Ya, Hapus Path
                                        </button>
                                    </form>
                                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all" @click="deleteModalOpen = false">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

             <!-- Edit Modal -->
             <template x-teleport="body">
                <div x-show="editModalOpen" class="relative z-[999]" style="display: none;">
                    <div x-show="editModalOpen" 
                        x-transition:enter="ease-out duration-300" 
                        x-transition:enter-start="opacity-0" 
                        x-transition:enter-end="opacity-100" 
                        x-transition:leave="ease-in duration-200" 
                        x-transition:leave-start="opacity-100" 
                        x-transition:leave-end="opacity-0" 
                        class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity">
                    </div>

                    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                            <div x-show="editModalOpen" 
                                @click.away="editModalOpen = false" 
                                class="relative transform overflow-hidden rounded-[2rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                                
                                <form :action="editActionUrl" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="bg-white px-4 pb-4 pt-5 sm:p-8 sm:pb-6 relative overflow-hidden">
                                        <div class="mb-5">
                                            <h3 class="text-xl font-bold leading-6 text-slate-900">Edit Tujuan Path</h3>
                                            <p class="text-slate-500 text-sm mt-2">Ubah alamat URL asli yang dituju oleh shortlink ini.</p>
                                        </div>
                                        
                                        <div class="group/input mb-4">
                                            <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Short Link / Alias</label>
                                            <div class="flex shadow-sm rounded-2xl overflow-hidden border border-slate-200 focus-within:ring-4 focus-within:ring-purple-500/10 focus-within:border-purple-500 transition-all">
                                                <span class="inline-flex items-center px-4 bg-slate-50 text-slate-500 text-sm font-bold border-r border-slate-100">/</span>
                                                <input type="text" name="short_code" :value="oldShortCode" placeholder="alias-baru" 
                                                    class="flex-1 min-w-0 block w-full px-4 py-3 bg-white border-transparent text-slate-900 placeholder-slate-400 focus:outline-none transition-all font-medium" required>
                                            </div>
                                        </div>

                                        <div class="group/input">
                                            <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">URL / Link Asli Tujuan</label>
                                            <div class="flex shadow-sm rounded-2xl overflow-hidden border border-slate-200 focus-within:ring-4 focus-within:ring-purple-500/10 focus-within:border-purple-500 transition-all">
                                                <input type="text" name="original_url" :value="oldOriginalUrl" placeholder="https://" 
                                                    class="flex-1 min-w-0 block w-full px-4 py-3 bg-white border-transparent text-slate-900 placeholder-slate-400 focus:outline-none transition-all font-medium" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50/50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                                        <button type="submit" class="inline-flex w-full justify-center rounded-full bg-blue-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-blue-500/30 hover:bg-blue-500 transition-all sm:w-auto">
                                            Simpan Perubahan
                                        </button>
                                        <button type="button" class="mt-3 inline-flex w-full justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all" @click="editModalOpen = false">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</x-app-layout>
