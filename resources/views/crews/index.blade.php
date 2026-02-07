<x-app-layout>
    <x-slot name="title">Crews Generator</x-slot>

    <style>
        @keyframes crew-card-in {
            0% { opacity: 0; transform: translateY(20px) scale(0.96); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }
        .crew-card-animate {
            animation: crew-card-in 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
        }
        @keyframes shuffle-pulse {
            0%, 100% { transform: scale(1); opacity: 0.6; }
            50% { transform: scale(1.15); opacity: 1; }
        }
        .shuffle-ring { animation: shuffle-pulse 0.8s ease-in-out infinite; }
        .gender-male { border-left: 3px solid #3b82f6; }
        .gender-female { border-left: 3px solid #ec4899; }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 relative isolate" x-data="crewsGenerator()">
        
        <!-- Background Decoration -->
        <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-indigo-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>
        <div class="absolute -top-20 -right-20 w-[500px] h-[500px] bg-violet-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>

        <div class="flex flex-col gap-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-slate-900 to-slate-700 tracking-tight mb-2">
                        Crews Mission Control
                    </h1>
                    <p class="text-lg text-slate-500 font-medium max-w-2xl">
                        Bentuk kru misi pembelajaran dengan generator kelompok otomatis dan adil.
                    </p>
                </div>
                
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/60 backdrop-blur-md rounded-full shadow-sm border border-white/50 text-sm font-bold text-slate-600">
                    <div class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></div>
                    <span x-text="nameCount + ' Kru Terdaftar'"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <!-- Left Column: Input & Settings -->
                <div class="lg:col-span-4 space-y-6">

                    <!-- 1. Daftar Nama Container -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white/50 shadow-sm p-8 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-violet-500 opacity-50"></div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">Daftar Kru</h3>
                                <p class="text-slate-500 text-sm">Satu nama per baris. Bisa paste dari Excel.</p>
                            </div>
                        </div>

                        <!-- Gender Mode Toggle -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <span class="text-sm font-bold text-slate-600">Mode Gender</span>
                                </div>
                                <div class="flex items-center gap-2 rounded-full pl-3 pr-1 py-1 border transition-all duration-300" :class="genderMode ? 'bg-indigo-50 border-indigo-200' : 'bg-slate-50 border-slate-200'">
                                    <span class="text-[10px] font-bold uppercase tracking-widest transition-colors duration-300" :class="genderMode ? 'text-indigo-600' : 'text-slate-400'" x-text="genderMode ? 'Aktif' : 'Non-Aktif'"></span>
                                    <button @click="genderMode = !genderMode" class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 focus:outline-none shadow-sm" :class="genderMode ? 'bg-indigo-500' : 'bg-slate-300'">
                                        <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-all duration-300 ease-in-out" :class="genderMode ? 'translate-x-6' : 'translate-x-1'"></span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Textarea for plain mode -->
                        <div x-show="!genderMode">
                            <textarea x-model="inputNames" class="w-full px-4 py-3 bg-slate-50 border-transparent rounded-2xl text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-medium resize-none shadow-inner text-sm leading-relaxed" rows="16" placeholder="Ahmad Fauzi&#10;Budi Santoso&#10;Citra Dewi&#10;Dina Rahmawati&#10;Eka Putra&#10;..."></textarea>
                        </div>

                        <!-- Gender-aware input -->
                        <div x-show="genderMode" class="space-y-3">
                            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-wider px-1">
                                <span class="flex-1">Nama</span>
                                <span class="w-20 text-center">Gender</span>
                            </div>
                            <div class="space-y-2 max-h-[400px] overflow-y-auto pr-1" style="scrollbar-width: thin;">
                                <template x-for="(student, idx) in students" :key="idx">
                                    <div class="flex items-center gap-2">
                                        <input type="text" x-model="student.name" @input="ensureEmptyRow()" class="flex-1 px-3 py-2 bg-slate-50 border-transparent rounded-xl text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-500/10 transition-all font-medium" :placeholder="'Nama siswa ' + (idx + 1)">
                                        <div class="flex gap-1 w-20">
                                            <button type="button" @click="student.gender = 'L'" :class="student.gender === 'L' ? 'bg-blue-500 text-white shadow-sm' : 'bg-slate-100 text-slate-400 hover:bg-blue-50 hover:text-blue-500'" class="w-9 h-9 rounded-full text-xs font-bold transition-all">L</button>
                                            <button type="button" @click="student.gender = 'P'" :class="student.gender === 'P' ? 'bg-pink-500 text-white shadow-sm' : 'bg-slate-100 text-slate-400 hover:bg-pink-50 hover:text-pink-500'" class="w-9 h-9 rounded-full text-xs font-bold transition-all">P</button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div class="flex items-center justify-between pt-2 px-1">
                                <div class="flex gap-3 text-xs font-bold">
                                    <span class="text-blue-500"><span x-text="maleCount"></span> Laki-laki</span>
                                    <span class="text-pink-500"><span x-text="femaleCount"></span> Perempuan</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center mt-4">
                            <span class="text-xs font-bold text-slate-400 ml-1" x-text="nameCount + ' nama'"></span>
                            <button type="button" @click="clearModalOpen = true" class="text-xs font-bold text-red-500 hover:text-red-600 transition-colors px-3 py-1.5 rounded-full hover:bg-red-50">Hapus Semua</button>
                        </div>
                    </div>

                    <!-- 2. Settings Container -->
                    <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white/50 shadow-sm p-8 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-violet-500 to-fuchsia-500 opacity-50"></div>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-fuchsia-600 text-white flex items-center justify-center shadow-lg shadow-violet-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">Pengaturan Misi</h3>
                                <p class="text-slate-500 text-sm">Konfigurasi pembagian kelompok.</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- Method -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Metode Pembagian</label>
                                <select x-model="method" class="w-full px-4 py-3 bg-slate-50 border-transparent rounded-2xl text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold cursor-pointer">
                                    <option value="groups">Berdasarkan Jumlah Kelompok</option>
                                    <option value="members">Berdasarkan Anggota per Kelompok</option>
                                </select>
                            </div>

                            <!-- Count -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1" x-text="method === 'groups' ? 'Jumlah Kelompok' : 'Anggota per Kelompok'"></label>
                                <div class="flex items-center gap-3">
                                    <button @click="count > 2 ? count-- : null" class="w-12 h-12 rounded-full bg-slate-50 border border-slate-200 text-slate-600 font-bold text-lg hover:bg-slate-100 active:scale-95 transition-all">-</button>
                                    <div class="flex-1 text-center">
                                        <span class="text-3xl font-extrabold text-slate-800" x-text="count"></span>
                                        <p class="text-xs font-bold text-slate-400 mt-1" x-text="method === 'groups' ? 'kelompok' : 'orang/kelompok'"></p>
                                    </div>
                                    <button @click="count++" class="w-12 h-12 rounded-full bg-slate-50 border border-slate-200 text-slate-600 font-bold text-lg hover:bg-slate-100 active:scale-95 transition-all">+</button>
                                </div>
                            </div>

                            <!-- Gender Balance -->
                            <div x-show="genderMode" x-transition>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-slate-700">Seimbangkan Gender</p>
                                        <p class="text-xs text-slate-500">Distribusi L/P merata</p>
                                    </div>
                                    <div class="flex items-center gap-2 rounded-full pl-3 pr-1 py-1 border transition-all duration-300" :class="balanceGender ? 'bg-indigo-50 border-indigo-200' : 'bg-slate-50 border-slate-200'">
                                        <span class="text-[10px] font-bold uppercase tracking-widest transition-colors duration-300" :class="balanceGender ? 'text-indigo-600' : 'text-slate-400'" x-text="balanceGender ? 'Aktif' : 'Non-Aktif'"></span>
                                        <button @click="balanceGender = !balanceGender" class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 focus:outline-none shadow-sm" :class="balanceGender ? 'bg-indigo-500' : 'bg-slate-300'">
                                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-all duration-300 ease-in-out" :class="balanceGender ? 'translate-x-6' : 'translate-x-1'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Auto Leader -->
                            <div>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-bold text-slate-700">Pilih Ketua Otomatis</p>
                                        <p class="text-xs text-slate-500">Acak 1 ketua per kelompok</p>
                                    </div>
                                    <div class="flex items-center gap-2 rounded-full pl-3 pr-1 py-1 border transition-all duration-300" :class="autoLeader ? 'bg-amber-50 border-amber-200' : 'bg-slate-50 border-slate-200'">
                                        <span class="text-[10px] font-bold uppercase tracking-widest transition-colors duration-300" :class="autoLeader ? 'text-amber-600' : 'text-slate-400'" x-text="autoLeader ? 'Aktif' : 'Non-Aktif'"></span>
                                        <button @click="autoLeader = !autoLeader" class="relative inline-flex h-7 w-12 items-center rounded-full transition-all duration-300 focus:outline-none shadow-sm" :class="autoLeader ? 'bg-amber-500' : 'bg-slate-300'">
                                            <span class="inline-block h-5 w-5 transform rounded-full bg-white shadow-md transition-all duration-300 ease-in-out" :class="autoLeader ? 'translate-x-6' : 'translate-x-1'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Crew Naming -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Penamaan Kelompok</label>
                                <select x-model="naming" class="w-full px-4 py-3 bg-slate-50 border-transparent rounded-2xl text-slate-900 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all font-bold cursor-pointer">
                                    <option value="greek">Alfabet Yunani (Alpha, Beta, ...)</option>
                                    <option value="planet">Planet (Mercury, Venus, ...)</option>
                                    <option value="number">Nomor (Kelompok 1, 2, ...)</option>
                                    <option value="color">Warna (Merah, Biru, ...)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Launch Button -->
                        <button @click="generateCrews" :disabled="nameCount < 2 || isGenerating" class="w-full mt-8 py-4 bg-gradient-to-r from-indigo-600 to-violet-600 rounded-full font-bold text-white shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3 group">
                            <template x-if="!isGenerating">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5 group-hover:rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Luncurkan Misi
                                </span>
                            </template>
                            <template x-if="isGenerating">
                                <span class="flex items-center gap-2">
                                    <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    Mengacak Kru...
                                </span>
                            </template>
                        </button>
                    </div>
                </div>

                <!-- Right Column: Results -->
                <div class="lg:col-span-8">

                    <!-- Reshuffle Floating Bar (shown when results exist) -->
                    <div x-show="groups.length > 0 && !isGenerating" x-transition class="sticky top-4 z-20 mb-6">
                        <div class="bg-white/90 backdrop-blur-xl rounded-full border border-white/50 shadow-lg px-4 py-2.5 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-2 text-sm font-bold text-slate-600 pl-2">
                                <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                                <span x-text="groups.length + ' Kelompok Terbentuk'"></span>
                            </div>
                            <button @click="generateCrews" class="inline-flex items-center gap-2 px-5 py-2 bg-gradient-to-r from-indigo-600 to-violet-600 rounded-full font-bold text-white text-sm shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/30 hover:scale-[1.03] active:scale-[0.97] transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                Acak Ulang
                            </button>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div x-show="groups.length === 0 && !isGenerating" class="flex flex-col items-center justify-center text-center p-16 bg-white/40 backdrop-blur-sm rounded-[2.5rem] border border-dashed border-slate-300 min-h-[600px]">
                        <div class="w-28 h-28 bg-indigo-50 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-700 mb-2">Belum Ada Misi Aktif</h3>
                        <p class="text-slate-500 max-w-sm text-sm leading-relaxed">Masukkan daftar nama kru di panel sebelah kiri, atur konfigurasi, lalu tekan "Luncurkan Misi".</p>
                    </div>

                    <!-- Loading State -->
                    <div x-show="isGenerating" class="flex flex-col items-center justify-center p-16 min-h-[600px]">
                        <div class="relative w-28 h-28 mb-8">
                            <div class="absolute inset-0 border-4 border-slate-200 rounded-full"></div>
                            <div class="absolute inset-0 border-4 border-indigo-500 rounded-full border-t-transparent animate-spin"></div>
                            <div class="absolute inset-2 border-4 border-violet-400 rounded-full border-b-transparent animate-spin shuffle-ring" style="animation-direction: reverse;"></div>
                            <div class="absolute inset-6 bg-gradient-to-br from-indigo-100 to-violet-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-slate-700 mb-1">Menganalisa & Mengacak Kru</h3>
                        <p class="text-slate-500 text-sm">Membentuk formasi terbaik...</p>
                    </div>

                    <!-- Results Grid -->
                    <div x-show="groups.length > 0 && !isGenerating" class="space-y-6">
                        <!-- Stats Bar -->
                        <div class="bg-white/80 backdrop-blur-xl rounded-2xl border border-white/50 shadow-sm px-6 py-4 flex flex-wrap items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 font-bold">Kelompok</p>
                                        <p class="text-lg font-extrabold text-slate-800" x-text="groups.length"></p>
                                    </div>
                                </div>
                                <div class="w-px h-10 bg-slate-200"></div>
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-violet-100 flex items-center justify-center">
                                        <svg class="w-4 h-4 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500 font-bold">Total Anggota</p>
                                        <p class="text-lg font-extrabold text-slate-800" x-text="nameCount"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Groups Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <template x-for="(group, index) in groups" :key="index">
                                <div class="crew-card-animate bg-white/80 backdrop-blur-xl rounded-[2rem] border border-white/50 shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden" :style="`animation-delay: ${index * 80}ms`">
                                    <!-- Group Header -->
                                    <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-indigo-200">
                                                <span x-text="index + 1"></span>
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-800" x-text="getGroupName(index)"></span>
                                                <p class="text-xs text-slate-400 font-medium" x-text="group.length + ' anggota'"></p>
                                            </div>
                                        </div>
                                        <!-- Gender stats if gender mode -->
                                        <div x-show="genderMode" class="flex gap-2">
                                            <span class="text-xs font-bold text-blue-500 bg-blue-50 px-2.5 py-1 rounded-lg" x-text="getGroupMaleCount(group) + ' L'"></span>
                                            <span class="text-xs font-bold text-pink-500 bg-pink-50 px-2.5 py-1 rounded-lg" x-text="getGroupFemaleCount(group) + ' P'"></span>
                                        </div>
                                    </div>
                                    
                                    <!-- Members List -->
                                    <div class="p-5">
                                        <ul class="space-y-2">
                                            <template x-for="(member, mIdx) in group" :key="mIdx">
                                                <li class="flex items-center gap-3 py-2 px-3 rounded-xl transition-colors" :class="[
                                                    genderMode ? (member.gender === 'L' ? 'gender-male' : member.gender === 'P' ? 'gender-female' : '') : '',
                                                    member.isLeader ? 'bg-amber-50 ring-1 ring-amber-200' : 'hover:bg-slate-50'
                                                ]">
                                                    <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" :class="member.isLeader ? 'bg-amber-100 text-amber-700' : (genderMode && member.gender === 'P' ? 'bg-pink-100 text-pink-600' : 'bg-indigo-100 text-indigo-600')">
                                                        <template x-if="member.isLeader">
                                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M5 16L3 5l5.5 5L12 4l3.5 6L21 5l-2 11H5zm0 2h14v2H5v-2z"/></svg>
                                                        </template>
                                                        <template x-if="!member.isLeader">
                                                            <span x-text="(typeof member === 'string' ? member : member.name).charAt(0).toUpperCase()"></span>
                                                        </template>
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <span class="text-sm font-medium text-slate-700" x-text="typeof member === 'string' ? member : member.name"></span>
                                                        <template x-if="member.isLeader">
                                                            <span class="ml-2 text-[10px] font-bold text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full">Ketua</span>
                                                        </template>
                                                    </div>
                                                    <template x-if="genderMode && member.gender">
                                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="member.gender === 'L' ? 'text-blue-500 bg-blue-50' : 'text-pink-500 bg-pink-50'" x-text="member.gender === 'L' ? 'L' : 'P'"></span>
                                                    </template>
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Clear All Confirmation Modal -->
        <template x-teleport="body">
            <div x-show="clearModalOpen" class="relative z-[999]" aria-labelledby="modal-title" role="dialog" aria-modal="true" style="display: none;">
                <div x-show="clearModalOpen" 
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
                        <div x-show="clearModalOpen" 
                            @click.away="clearModalOpen = false"
                            @keydown.escape.window="clearModalOpen = false"
                            x-transition:enter="ease-out duration-300" 
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                            x-transition:leave="ease-in duration-200" 
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                            class="relative transform overflow-hidden rounded-[2rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                            
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-8 sm:pb-6 relative overflow-hidden">
                                <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-50 rounded-full blur-2xl"></div>
                                <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-orange-50 rounded-full blur-2xl"></div>

                                <div class="sm:flex sm:items-start relative z-10">
                                    <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-red-50 sm:mx-0 sm:h-12 sm:w-12 border border-red-100 shadow-sm ring-4 ring-red-50/50">
                                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <h3 class="text-xl font-bold leading-6 text-slate-900">Hapus Semua Data?</h3>
                                        <div class="mt-3">
                                            <p class="text-slate-500 text-sm leading-relaxed">
                                                Anda yakin ingin menghapus seluruh daftar nama kru dan hasil kelompok yang sudah dibentuk?
                                            </p>
                                            <div class="mt-4 p-3 bg-red-50/50 rounded-2xl border border-red-100 text-xs text-slate-600 flex gap-3 items-start text-left">
                                                <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p>Semua nama, pengaturan gender, dan hasil kelompok akan <span class="font-bold text-red-600">dihapus permanen</span>. Tindakan ini tidak dapat dibatalkan.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50/50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                                <button type="button" @click="clearAll" class="inline-flex w-full justify-center rounded-full bg-red-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-red-500/30 hover:bg-red-500 hover:scale-[1.02] transition-all sm:w-auto">
                                    Ya, Hapus Semua
                                </button>
                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all" @click="clearModalOpen = false">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function crewsGenerator() {
            return {
                inputNames: '',
                genderMode: false,
                balanceGender: true,
                autoLeader: false,
                clearModalOpen: false,
                method: 'groups',
                count: 4,
                naming: 'greek',
                isGenerating: false,
                groups: [],
                students: [{ name: '', gender: '' }],
                
                nameSets: {
                    greek: ['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta', 'Eta', 'Theta', 'Iota', 'Kappa', 'Lambda', 'Mu', 'Nu', 'Xi', 'Omicron', 'Pi'],
                    planet: ['Mercury', 'Venus', 'Earth', 'Mars', 'Jupiter', 'Saturn', 'Uranus', 'Neptune', 'Pluto', 'Luna', 'Titan', 'Europa', 'Ganymede', 'Callisto', 'Io', 'Triton'],
                    number: [],
                    color: ['Merah', 'Biru', 'Hijau', 'Kuning', 'Ungu', 'Oranye', 'Putih', 'Hitam', 'Abu-Abu', 'Coklat', 'Cyan', 'Magenta', 'Emas', 'Perak', 'Tosca', 'Koral']
                },
                
                get nameList() {
                    if (this.genderMode) {
                        return this.students.filter(s => s.name.trim().length > 0);
                    }
                    return this.inputNames.split('\n').map(n => n.trim()).filter(n => n.length > 0);
                },
                
                get nameCount() {
                    return this.nameList.length;
                },
                
                get maleCount() {
                    return this.students.filter(s => s.name.trim() && s.gender === 'L').length;
                },
                
                get femaleCount() {
                    return this.students.filter(s => s.name.trim() && s.gender === 'P').length;
                },
                
                ensureEmptyRow() {
                    const lastStudent = this.students[this.students.length - 1];
                    if (lastStudent && lastStudent.name.trim().length > 0) {
                        this.students.push({ name: '', gender: '' });
                    }
                },
                
                clearAll() {
                    this.inputNames = '';
                    this.students = [{ name: '', gender: '' }];
                    this.groups = [];
                    this.clearModalOpen = false;
                },
                
                getGroupName(index) {
                    if (this.naming === 'number') return 'Kelompok ' + (index + 1);
                    return 'Kru ' + (this.nameSets[this.naming]?.[index] || (index + 1));
                },
                
                getGroupMaleCount(group) {
                    return group.filter(m => m.gender === 'L').length;
                },
                
                getGroupFemaleCount(group) {
                    return group.filter(m => m.gender === 'P').length;
                },
                
                shuffleArray(arr) {
                    const a = [...arr];
                    for (let i = a.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [a[i], a[j]] = [a[j], a[i]];
                    }
                    return a;
                },
                
                generateCrews() {
                    if (this.nameCount < 2) return;
                    this.isGenerating = true;
                    this.groups = [];
                    
                    setTimeout(() => {
                        let result = [];
                        
                        if (this.genderMode && this.balanceGender) {
                            // Gender-balanced distribution
                            const males = this.shuffleArray(this.nameList.filter(s => s.gender === 'L'));
                            const females = this.shuffleArray(this.nameList.filter(s => s.gender === 'P'));
                            const unset = this.shuffleArray(this.nameList.filter(s => s.gender !== 'L' && s.gender !== 'P'));
                            
                            let numGroups;
                            if (this.method === 'groups') {
                                numGroups = Math.min(this.count, this.nameCount);
                            } else {
                                numGroups = Math.max(1, Math.ceil(this.nameCount / this.count));
                            }
                            
                            for (let i = 0; i < numGroups; i++) result.push([]);
                            
                            // Distribute males
                            males.forEach((m, i) => result[i % numGroups].push(m));
                            // Distribute females
                            females.forEach((f, i) => result[i % numGroups].push(f));
                            // Distribute unset
                            unset.forEach((u, i) => result[i % numGroups].push(u));
                        } else {
                            // Standard distribution
                            let names;
                            if (this.genderMode) {
                                names = this.shuffleArray(this.nameList);
                            } else {
                                names = this.shuffleArray(this.nameList);
                            }
                            
                            if (this.method === 'groups') {
                                const numGroups = Math.min(this.count, names.length);
                                for (let i = 0; i < numGroups; i++) result.push([]);
                                names.forEach((name, i) => result[i % numGroups].push(name));
                            } else {
                                const size = Math.max(1, this.count);
                                for (let i = 0; i < names.length; i += size) {
                                    result.push(names.slice(i, i + size));
                                }
                            }
                        }
                        
                        // Assign leader if enabled
                        if (this.autoLeader) {
                            result.forEach(group => {
                                if (group.length > 0) {
                                    const leaderIdx = Math.floor(Math.random() * group.length);
                                    group.forEach((m, i) => {
                                        if (typeof m === 'string') {
                                            group[i] = { name: m, isLeader: i === leaderIdx };
                                        } else {
                                            m.isLeader = i === leaderIdx;
                                        }
                                    });
                                    // Move leader to top
                                    const leader = group.splice(leaderIdx, 1)[0];
                                    group.unshift(leader);
                                }
                            });
                        } else {
                            // Ensure members have isLeader = false
                            result.forEach(group => {
                                group.forEach(m => {
                                    if (typeof m !== 'string') m.isLeader = false;
                                });
                            });
                        }

                        this.groups = result;
                        this.isGenerating = false;
                    }, 1000);
                }
            };
        }
    </script>
</x-app-layout>
