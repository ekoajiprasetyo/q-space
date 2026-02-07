<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 relative isolate">
        
        <!-- Background Decor (Dashboard Style) -->
        <div class="absolute top-0 left-[-10%] w-[500px] h-[500px] bg-blue-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>
        <div class="absolute bottom-0 right-[-10%] w-[500px] h-[500px] bg-purple-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>

        <!-- Main Container -->
        <div class="relative w-full max-w-6xl mx-auto bg-white/60 backdrop-blur-2xl rounded-[3rem] shadow-[0_8px_40px_rgba(0,0,0,0.04)] overflow-hidden border border-white/60 flex flex-col md:flex-row min-h-[600px] z-10">
            
            <!-- Left Panel (Visual & Info) -->
            <div class="w-full md:w-2/5 bg-gradient-to-br from-slate-900 to-slate-800 p-10 md:p-14 text-white flex flex-col justify-between relative overflow-hidden group">
                <!-- Decorative Circle -->
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-blue-500/30 rounded-full blur-3xl group-hover:bg-blue-500/40 transition-all duration-700"></div>
                <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-purple-500/30 rounded-full blur-3xl group-hover:bg-purple-500/40 transition-all duration-700"></div>
                
                <div class="relative z-10">
                    <a href="{{ route('files.index') }}" class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-colors text-sm font-bold mb-8 group/back">
                        <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center group-hover/back:bg-white/20 transition-all">
                            <svg class="w-4 h-4 group-hover/back:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </div>
                        Kembali
                    </a>

                    <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-tight mb-4">
                        Buat<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">Permintaan</span><br>
                        Baru
                    </h1>
                    <p class="text-slate-400 font-medium leading-relaxed">
                        Folder otomatis akan dibuat di Google Drive Anda. Siswa dapat langsung mengunggah tugas mereka ke sana.
                    </p>
                </div>

                <!-- Abstract Folder Illustration -->
                <div class="relative mt-8 md:mt-0 transform md:translate-x-4 md:translate-y-4 transition-transform duration-700 md:group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-500 blur-2xl opacity-20 rounded-full"></div>
                    <div class="relative bg-white/5 border border-white/10 rounded-3xl p-6 backdrop-blur-md rotate-3 hover:rotate-0 transition-transform duration-500">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M20 6h-8l-2-2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm0 12H4V8h16v10z"></path></svg>
                            </div>
                            <div>
                                <div class="h-2 w-24 bg-white/20 rounded-full mb-2"></div>
                                <div class="h-2 w-16 bg-white/10 rounded-full"></div>
                            </div>
                        </div>
                        <div class="space-y-2">
                             <div class="h-24 rounded-xl bg-white/5 border border-white/5 flex items-center justify-center border-dashed">
                                <span class="text-xs text-slate-500 font-medium">Area Upload Siswa</span>
                             </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel (Form) -->
            <div class="w-full md:w-3/5 p-8 md:p-14 bg-transparent overflow-y-auto">
                <form action="{{ route('file-requests.store') }}" method="POST" class="h-full flex flex-col" x-data="{ isCreating: false }" @submit="isCreating = true">
                    @csrf
                    
                    <div class="flex-1 space-y-8">
                        <!-- Custom Input: Title -->
                        <div class="group">
                            <label for="title" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-blue-600 transition-colors">Judul Permintaan</label>
                            <div class="relative">
                                <input 
                                    id="title" 
                                    type="text" 
                                    name="title" 
                                    value="{{ old('title') }}" 
                                    required 
                                    autofocus 
                                    placeholder="Tulis judul tugas di sini..." 
                                    class="block w-full border-0 border-b-2 border-slate-200 bg-transparent py-3 px-0 text-lg md:text-xl font-bold text-slate-800 focus:border-blue-500 focus:ring-0 placeholder-slate-300 transition-all font-display"
                                />
                                <div class="absolute right-0 top-1/2 -translate-y-1/2 opacity-0 group-focus-within:opacity-100 transition-opacity text-blue-500">
                                    <svg class="w-6 h-6 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <!-- Custom Input: Description -->
                         <div class="group">
                            <label for="description" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-blue-600 transition-colors">Instruksi Detail</label>
                             <div class="relative bg-slate-50 rounded-3xl p-4 transition-all focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:bg-white">
                                <textarea 
                                    id="description" 
                                    name="description" 
                                    rows="4" 
                                    class="block w-full border-0 bg-transparent p-0 text-slate-800 placeholder-slate-400 focus:ring-0 resize-none font-medium leading-relaxed"
                                    placeholder="Berikan instruksi yang jelas agar siswa tidak bingung..."
                                >{{ old('description') }}</textarea>
                            </div>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Custom Input: Max Files -->
                        <div class="group">
                            <label for="max_files" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-blue-600 transition-colors">Jumlah File (Per Siswa)</label>
                            <label for="max_files" class="flex items-center gap-4 p-4 rounded-3xl border border-slate-200 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all group-focus-within:border-blue-500 group-focus-within:ring-4 group-focus-within:ring-blue-500/10">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <input 
                                        id="max_files" 
                                        type="number" 
                                        name="max_files"
                                        min="1"
                                        value="{{ old('max_files', 1) }}"
                                        required
                                        class="block w-full border-0 bg-transparent p-0 text-slate-800 font-bold text-xl focus:ring-0"
                                    />
                                    <p class="text-xs text-slate-400 mt-0.5">Berapa banyak file yang harus diupload siswa?</p>
                                </div>
                            </label>
                            <x-input-error :messages="$errors->get('max_files')" class="mt-2" />
                        </div>

                        <!-- Custom Input: Date -->
                         <div class="group">
                            <label for="deadline" class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-2 group-focus-within:text-blue-600 transition-colors">Tenggat Waktu</label>
                            <label for="deadline" class="flex items-center gap-4 p-4 rounded-3xl border border-slate-200 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition-all group-focus-within:border-blue-500 group-focus-within:ring-4 group-focus-within:ring-blue-500/10 mb-1">
                                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <div class="flex-1">
                                    <input 
                                        id="deadline" 
                                        type="datetime-local" 
                                        name="deadline" 
                                        value="{{ old('deadline') }}" 
                                        class="block w-full border-0 bg-transparent p-0 text-slate-800 font-bold focus:ring-0 cursor-pointer"
                                    />
                                    <p class="text-xs text-slate-400 mt-0.5">Kosongkan jika tidak ada batas waktu</p>
                                </div>
                            </label>
                            <p class="text-xs text-amber-600 font-medium px-2 flex items-center gap-1.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                Siswa masih dapat mengirim tugas melewati tenggat waktu, namun akan ditandai "Terlambat".
                            </p>
                            <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-10 flex items-center justify-end gap-3 md:gap-4">
                        <a href="{{ route('files.index') }}" class="px-6 py-3 rounded-full text-slate-500 font-bold hover:bg-slate-100 transition-all">
                            Batal
                        </a>
                        <button type="submit" 
                            class="group relative px-8 py-3 bg-slate-900 text-white rounded-full font-bold overflow-hidden shadow-xl hover:shadow-2xl hover:shadow-blue-500/20 transition-all hover:scale-[1.02] active:scale-95 disabled:opacity-70 disabled:pointer-events-none"
                            :disabled="isCreating">
                            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-blue-500 to-indigo-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Normal State -->
                            <div class="relative flex items-center gap-2" x-show="!isCreating">
                                <span>Buat Sekarang</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </div>

                            <!-- Loading State -->
                            <div class="relative flex items-center gap-2" x-show="isCreating" style="display: none;">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memproses...</span>
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
