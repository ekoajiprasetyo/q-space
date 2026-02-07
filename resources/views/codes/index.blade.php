<x-app-layout>
    <x-slot name="title">QR Code Generator</x-slot>

    <!-- Quill.js Editor -->
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <style>
        .ql-toolbar.ql-snow {
            border: none;
            border-bottom: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 1rem 1rem 0 0;
            padding: 12px;
        }
        .ql-container.ql-snow {
            border: none;
            font-family: 'Figtree', sans-serif;
            font-size: 1rem;
        }
        .ql-editor {
            min-height: 200px;
            padding: 16px;
            line-height: 1.7;
        }
        .ql-editor.ql-blank::before {
            font-style: normal;
            color: #94a3b8;
        }
        #text-editor {
            border: 2px solid #e2e8f0;
            transition: all 0.2s;
        }
        #text-editor:focus-within {
            border-color: #14b8a6;
            box-shadow: 0 0 0 4px rgba(20, 184, 166, 0.1);
        }
        .qr-type-btn {
            border-color: #e2e8f0;
            background: #f8fafc;
            color: #475569;
        }
        .qr-type-btn:hover {
            border-color: #cbd5e1;
        }
        .qr-type-btn.active {
            border-color: #14b8a6 !important;
            background: #f0fdfa !important;
            color: #0f766e !important;
        }
    </style>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20 relative isolate">
        
        <!-- Background Decoration -->
        <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-blue-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>
        <div class="absolute -top-20 -right-20 w-[500px] h-[500px] bg-purple-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>

        <div class="flex flex-col gap-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-slate-900 to-slate-700 tracking-tight mb-2">
                        QR Studio Pro
                    </h1>
                    <p class="text-lg text-slate-500 font-medium max-w-2xl">
                        Buat kode QR profesional dengan gaya kustom, logo, dan pilihan resolusi tinggi.
                    </p>
                </div>
                
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/60 backdrop-blur-md rounded-full shadow-sm border border-white/50 text-sm font-bold text-slate-600">
                    <div class="w-2 h-2 rounded-full bg-teal-500 animate-pulse"></div>
                    Real-time Studio
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <!-- Configuration Panel -->
                <div class="lg:col-span-8">
                    <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white/50 shadow-sm p-8 relative overflow-hidden group">
                         <!-- Decorative Top Border -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-teal-500 to-emerald-500 opacity-50"></div>

                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-teal-500 to-emerald-600 text-white flex items-center justify-center shadow-lg shadow-teal-500/20">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-slate-800">Kustomisasi</h3>
                                <p class="text-slate-500 text-sm">Desain QR code Anda agar sesuai dengan identitas brand.</p>
                            </div>
                        </div>
                        
                        <div class="space-y-8">
                            <!-- QR Type Selector -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-3 ml-1">Tipe QR Code</label>
                                <div class="flex gap-3">
                                    <button type="button" id="type-url" class="qr-type-btn active flex-1 flex items-center justify-center gap-3 p-4 rounded-2xl border-2 font-bold transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                        <span>Link</span>
                                    </button>
                                    <button type="button" id="type-text" class="qr-type-btn flex-1 flex items-center justify-center gap-3 p-4 rounded-2xl border-2 border-slate-200 bg-slate-50 text-slate-600 font-bold transition-all hover:border-slate-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span>Teks</span>
                                    </button>
                                </div>
                            </div>

                            <!-- URL Input Section -->
                            <div id="url-section" class="group/input relative space-y-4">
                                <!-- Mode Toggle -->
                                <div class="flex items-center gap-4 bg-slate-50 p-2 rounded-2xl border border-slate-100 w-fit">
                                    <button type="button" id="mode-static" class="px-4 py-2 rounded-xl text-sm font-bold transition-all bg-white text-teal-700 shadow-sm border border-slate-200">Statis</button>
                                    <button type="button" id="mode-dynamic" class="px-4 py-2 rounded-xl text-sm font-bold text-slate-500 hover:text-slate-700 transition-all">Dinamis</button>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">URL / Link</label>
                                    <div class="relative">
                                        <textarea id="qr-text" rows="2" class="block w-full px-4 py-3 bg-slate-50 border-transparent rounded-2xl text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 transition-all font-medium resize-none shadow-inner" placeholder="Masukkan Link tujuan..."></textarea>
                                    </div>
                                    <p id="dynamic-hint" class="hidden text-xs text-slate-500 mt-2 ml-1 font-medium">Link akan dipendekkan dan statistik scan dapat dilacak.</p>
                                </div>
                                
                                <button type="button" id="generate-dynamic-btn" class="hidden w-full flex justify-center items-center py-4 bg-gradient-to-r from-teal-500 to-emerald-600 rounded-2xl font-bold text-white shadow-lg shadow-teal-500/30 hover:shadow-teal-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                                    Generate Dynamic QR
                                </button>
                            </div>

                            <!-- Rich Text Section (Hidden by default) -->
                            <div id="text-section" class="hidden space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Judul (Opsional)</label>
                                    <input type="text" id="text-title" class="block w-full px-4 py-3 bg-slate-50 border-transparent rounded-2xl text-slate-900 placeholder-slate-400 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 transition-all font-medium" placeholder="Judul konten...">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Konten</label>
                                    <div id="text-editor" class="bg-white rounded-2xl border border-slate-200 overflow-hidden"></div>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Tema Tampilan</label>
                                    <select id="text-theme" class="block w-full px-4 py-3 bg-slate-50 border-transparent rounded-2xl text-slate-900 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 transition-all font-bold cursor-pointer">
                                        <option value="default">Default (Light)</option>
                                        <option value="dark">Dark Mode</option>
                                        <option value="elegant">Elegant (Warm)</option>
                                        <option value="colorful">Colorful (Gradient)</option>
                                    </select>
                                </div>

                                <button type="button" id="generate-text-qr" class="w-full flex justify-center items-center py-4 bg-gradient-to-r from-teal-500 to-emerald-600 rounded-2xl font-bold text-white shadow-lg shadow-teal-500/30 hover:shadow-teal-500/40 hover:scale-[1.02] active:scale-[0.98] transition-all">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                    Generate QR Code
                                </button>
                                
                                <div id="text-qr-status" class="hidden">
                                    <div class="flex items-center gap-3 p-4 bg-teal-50 rounded-2xl border border-teal-200">
                                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <div class="flex-1">
                                            <p class="text-sm font-bold text-teal-800">QR Code berhasil dibuat!</p>
                                            <p class="text-xs text-teal-600" id="text-qr-url"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Logo & Image -->
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Logo (Opsional)</label>
                                <div class="flex items-center gap-4">
                                    <label class="flex items-center justify-center w-full px-4 py-3 bg-slate-50 border-2 border-dashed border-slate-300 rounded-2xl cursor-pointer hover:border-teal-500 hover:bg-white transition-all group">
                                        <div class="flex items-center gap-2 text-slate-500 group-hover:text-teal-600">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            <span class="text-sm font-medium" id="file-name">Pilih file logo...</span>
                                        </div>
                                        <input type="file" id="qr-logo" class="hidden" accept="image/*">
                                    </label>
                                    <button id="clear-logo" class="hidden px-4 py-3 bg-red-50 text-red-600 rounded-2xl font-bold text-sm hover:bg-red-100 transition-colors">Hapus</button>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <!-- Styles -->
                                <div class="space-y-6">
                                    <h4 class="text-sm font-black text-slate-400 uppercase tracking-wider">Tampilan</h4>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Bentuk Titik (Dots)</label>
                                        <select id="qr-dots-type" class="block w-full px-4 py-3 bg-slate-50 border-transparent rounded-2xl text-slate-900 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 transition-all font-bold cursor-pointer">
                                            <option value="square">Square (Kotak)</option>
                                            <option value="dots" selected>Dots (Lingkaran)</option>
                                            <option value="rounded">Rounded (Bulat)</option>
                                            <option value="classy">Classy</option>
                                            <option value="classy-rounded">Classy Rounded</option>
                                            <option value="extra-rounded">Extra Rounded</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Bentuk Sudut (Corners)</label>
                                        <select id="qr-corners-type" class="block w-full px-4 py-3 bg-slate-50 border-transparent rounded-2xl text-slate-900 focus:border-teal-500 focus:bg-white focus:ring-4 focus:ring-teal-500/10 transition-all font-bold cursor-pointer">
                                            <option value="square">Square (Kotak)</option>
                                            <option value="dot">Dot (Titik)</option>
                                            <option value="extra-rounded" selected>Extra Rounded</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Colors -->
                                <div class="space-y-6">
                                    <h4 class="text-sm font-black text-slate-400 uppercase tracking-wider">Warna</h4>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Warna Utama</label>
                                        <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-2xl border border-slate-100">
                                            <div class="relative w-10 h-10 flex-shrink-0 overflow-hidden rounded-xl shadow-sm ring-1 ring-slate-200">
                                                <input type="color" id="qr-fg-color" value="#0f172a" class="absolute -top-1/2 -left-1/2 w-[200%] h-[200%] p-0 border-0 cursor-pointer">
                                            </div>
                                            <input type="text" id="qr-fg-text" value="#0f172a" class="flex-1 bg-transparent border-none text-slate-600 font-mono text-xs focus:ring-0 uppercase" readonly>
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 mb-2 ml-1">Warna Background</label>
                                        <div class="flex items-center gap-3 bg-slate-50 p-2 rounded-2xl border border-slate-100">
                                            <div class="relative w-10 h-10 flex-shrink-0 overflow-hidden rounded-xl shadow-sm ring-1 ring-slate-200">
                                                <input type="color" id="qr-bg-color" value="#ffffff" class="absolute -top-1/2 -left-1/2 w-[200%] h-[200%] p-0 border-0 cursor-pointer">
                                            </div>
                                            <input type="text" id="qr-bg-text" value="#ffffff" class="flex-1 bg-transparent border-none text-slate-600 font-mono text-xs focus:ring-0 uppercase" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Panel -->
                <div class="lg:col-span-4">
                    <div class="bg-white/80 backdrop-blur-xl rounded-[2.5rem] border border-white/50 shadow-xl shadow-slate-200/50 sticky top-8">
                        <div class="p-8 flex flex-col items-center">
                            <span class="inline-block px-3 py-1 rounded-full bg-slate-100 text-slate-500 text-xs font-bold uppercase tracking-wider mb-6">Live Preview</span>
                            
                            <!-- Canvas Container -->
                            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm mb-8 w-full p-8">
                                <div id="qr-canvas"></div>
                            </div>
                            <style>
                                #qr-canvas {
                                    width: 100%;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                }
                                #qr-canvas > svg {
                                    width: 100% !important;
                                    height: auto !important;
                                    max-width: 100%;
                                    display: block;
                                }
                            </style>

                            <div class="w-full space-y-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 mb-2 ml-1 uppercase tracking-wide">Resolusi Download</label>
                                    <select id="qr-size" class="block w-full px-4 py-2 bg-slate-50 border-transparent rounded-xl text-slate-900 text-sm font-bold cursor-pointer mb-4">
                                        <option value="512">512x512 px (Standar)</option>
                                        <option value="1024" selected>1024x1024 px (HD)</option>
                                        <option value="2048">2048x2048 px (Full HD)</option>
                                        <option value="4096">4096x4096 px (4K)</option>
                                    </select>
                                </div>

                                <button id="download-btn" class="w-full flex justify-center items-center py-4 bg-slate-900 rounded-full font-bold text-white shadow-lg shadow-slate-900/30 hover:scale-[1.02] hover:shadow-slate-900/40 active:scale-[0.98] transition-all duration-300 group">
                                    <span class="mr-2">Download PNG</span>
                                    <svg class="w-5 h-5 group-hover:translate-y-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <!-- Global Toast System is used instead (App.blade.php) -->

    <!-- QR Code Styling Library -->
    <script type="text/javascript" src="https://unpkg.com/qr-code-styling@1.5.0/lib/qr-code-styling.js"></script>
    <!-- Quill.js -->
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const qrCanvas = document.getElementById('qr-canvas');
            const textInput = document.getElementById('qr-text');
            const fgColorInput = document.getElementById('qr-fg-color');
            const bgColorInput = document.getElementById('qr-bg-color');
            const dotsTypeInput = document.getElementById('qr-dots-type');
            const cornersTypeInput = document.getElementById('qr-corners-type');
            const logoInput = document.getElementById('qr-logo');
            const clearLogoBtn = document.getElementById('clear-logo');
            const fileNameSpan = document.getElementById('file-name');
            const downloadBtn = document.getElementById('download-btn');
            const sizeInput = document.getElementById('qr-size');
            
            // Helper to use Global Toast System (from app.blade.php)
            function showToast(title, message, type = 'success') {
                // Title is ignored by the simple global system, we combine it or just use message
                // The global system expects: notification.message
                window.dispatchEvent(new CustomEvent('notify', { 
                    detail: { 
                        message: message, 
                        type: type 
                    } 
                }));
            }
            
            // QR Type Elements
            const typeUrlBtn = document.getElementById('type-url');
            const typeTextBtn = document.getElementById('type-text');
            const urlSection = document.getElementById('url-section');
            const textSection = document.getElementById('text-section');
            const generateTextQrBtn = document.getElementById('generate-text-qr');
            const textQrStatus = document.getElementById('text-qr-status');
            const textQrUrl = document.getElementById('text-qr-url');
            const textTitleInput = document.getElementById('text-title');
            const textThemeSelect = document.getElementById('text-theme');
            
            const modeStaticBtn = document.getElementById('mode-static');
            const modeDynamicBtn = document.getElementById('mode-dynamic');
            const generateDynamicBtn = document.getElementById('generate-dynamic-btn');
            const dynamicHint = document.getElementById('dynamic-hint');
            
            let currentType = 'url';
            let urlMode = 'static';
            let dynamicUrl = '';

            // Mode Switching
            modeStaticBtn.addEventListener('click', () => {
                urlMode = 'static';
                modeStaticBtn.classList.add('bg-white', 'text-teal-700', 'shadow-sm', 'border', 'border-slate-200');
                modeStaticBtn.classList.remove('text-slate-500');
                modeDynamicBtn.classList.remove('bg-white', 'text-teal-700', 'shadow-sm', 'border', 'border-slate-200');
                modeDynamicBtn.classList.add('text-slate-500');
                dynamicHint.classList.add('hidden');
                generateDynamicBtn.classList.add('hidden');
                updateQR(); // Revert to static text immediately
            });

            modeDynamicBtn.addEventListener('click', () => {
                urlMode = 'dynamic';
                modeDynamicBtn.classList.add('bg-white', 'text-teal-700', 'shadow-sm', 'border', 'border-slate-200');
                modeDynamicBtn.classList.remove('text-slate-500');
                modeStaticBtn.classList.remove('bg-white', 'text-teal-700', 'shadow-sm', 'border', 'border-slate-200');
                modeStaticBtn.classList.add('text-slate-500');
                dynamicHint.classList.remove('hidden');
                generateDynamicBtn.classList.remove('hidden');
            });

            generateDynamicBtn.addEventListener('click', async () => {
                const url = textInput.value;
                if (!url.startsWith('http')) {
                    alert('Harap masukkan URL yang valid (dimulai dengan http/https)');
                    return;
                }

                generateDynamicBtn.disabled = true;
                generateDynamicBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...';

                try {
                    const response = await fetch('{{ route("codes.dynamic") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ url })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        dynamicUrl = data.short_url;
                        updateQR(); 
                        showToast('Berhasil!', 'Link QR Dinamis telah dibuat dan disimpan di menu Paths.');
                    } else {
                        showToast('Gagal', 'Gagal membuat dynamic link.', 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showToast('Error', 'Terjadi kesalahan koneksi.', 'error');
                } finally {
                    generateDynamicBtn.disabled = false;
                    generateDynamicBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg> Generate Dynamic QR';
                }
            });
            
            // Initialize Quill Editor
            const quill = new Quill('#text-editor', {
                theme: 'snow',
                placeholder: 'Tulis konten teks Anda di sini...',
                modules: {
                    toolbar: [
                        [{ 'header': [1, 2, 3, false] }],
                        ['bold', 'italic', 'underline', 'strike'],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'align': [] }],
                        ['blockquote', 'code-block'],
                        ['link'],
                        ['clean']
                    ]
                }
            });
            
            // Type Switching
            typeUrlBtn.addEventListener('click', () => {
                currentType = 'url';
                typeUrlBtn.classList.add('active');
                typeTextBtn.classList.remove('active');
                urlSection.classList.remove('hidden');
                textSection.classList.add('hidden');
                textQrStatus.classList.add('hidden');
            });
            
            typeTextBtn.addEventListener('click', () => {
                currentType = 'text';
                typeTextBtn.classList.add('active');
                typeUrlBtn.classList.remove('active');
                textSection.classList.remove('hidden');
                urlSection.classList.add('hidden');
            });
            
            // Generate Text QR Code
            generateTextQrBtn.addEventListener('click', async () => {
                const content = quill.root.innerHTML;
                const title = textTitleInput.value;
                const theme = textThemeSelect.value;
                
                if (quill.getText().trim().length < 1) {
                    alert('Silakan masukkan konten teks terlebih dahulu.');
                    return;
                }
                
                generateTextQrBtn.disabled = true;
                generateTextQrBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Generating...';
                
                try {
                    const response = await fetch('{{ route("qr-text.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ title, content, theme })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Update QR Code with the new URL
                        textInput.value = data.url;
                        qrCode.update({ data: data.url });
                        fixSvgViewBox();
                        
                        // Show success status
                        textQrUrl.textContent = data.url;
                        textQrStatus.classList.remove('hidden');
                    } else {
                        alert('Gagal membuat QR Code. Silakan coba lagi.');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                } finally {
                    generateTextQrBtn.disabled = false;
                    generateTextQrBtn.innerHTML = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg> Generate QR Code';
                }
            });

            // Sync color inputs with text display
            fgColorInput.addEventListener('input', (e) => document.getElementById('qr-fg-text').value = e.target.value);
            bgColorInput.addEventListener('input', (e) => document.getElementById('qr-bg-text').value = e.target.value);

            let logoUrl = null;

            // Initialize QRCodeStyling
            const qrCode = new QRCodeStyling({
                width: 300, 
                height: 300,
                margin: 10, // Add margin to prevent clipping
                type: "svg", 
                data: textInput.value,
                image: "",
                dotsOptions: {
                    color: fgColorInput.value,
                    type: dotsTypeInput.value
                },
                backgroundOptions: {
                    color: bgColorInput.value,
                },
                cornersSquareOptions: {
                    type: cornersTypeInput.value,
                    color: fgColorInput.value
                },
                cornersDotOptions: {
                    type: cornersTypeInput.value,
                    color: fgColorInput.value
                },
                imageOptions: {
                    crossOrigin: "anonymous",
                    margin: 5
                }
            });

            // Append to container
            qrCode.append(qrCanvas);

            // Fix SVG viewBox untuk responsive scaling
            const fixSvgViewBox = () => {
                setTimeout(() => {
                    const svg = qrCanvas.querySelector('svg');
                    if (svg) {
                        const width = svg.getAttribute('width') || 300;
                        const height = svg.getAttribute('height') || 300;
                        if (!svg.getAttribute('viewBox')) {
                            svg.setAttribute('viewBox', `0 0 ${width} ${height}`);
                        }
                        svg.removeAttribute('width');
                        svg.removeAttribute('height');
                        svg.style.width = '100%';
                        svg.style.height = 'auto';
                    }
                }, 50);
            };

            // Initial fix
            fixSvgViewBox();

            const updateQR = () => {
                let data = textInput.value;
                
                // Auto-prefix https:// if missing for URL type, but only if user typed something
                if (currentType === 'url' && textInput.value.length > 3 && !textInput.value.startsWith('http')) {
                    // We don't change the input value directly to avoid cursor jumping, just the QR data
                    data = "https://" + textInput.value;
                }
                
                // If in Dynamic Mode and we have a generated dynamic URL, use it
                if (currentType === 'url' && urlMode === 'dynamic' && dynamicUrl) {
                    data = dynamicUrl;
                }

                qrCode.update({
                    data: data,
                    dotsOptions: {
                        color: fgColorInput.value,
                        type: dotsTypeInput.value
                    },
                    backgroundOptions: {
                        color: bgColorInput.value,
                    },
                    cornersSquareOptions: {
                        type: cornersTypeInput.value,
                        color: fgColorInput.value
                    },
                    cornersDotOptions: {
                        type: cornersTypeInput.value,
                        color: fgColorInput.value
                    },
                    imageOptions: {
                         margin: 5
                    },
                    image: logoUrl
                });
                fixSvgViewBox();
            };

            // Event Listeners
            [textInput, fgColorInput, bgColorInput, dotsTypeInput, cornersTypeInput].forEach(el => {
                el.addEventListener('input', () => {
                    // Reset dynamic URL if input changes in dynamic mode (force user to regenerate)
                    if (el === textInput && urlMode === 'dynamic') {
                        dynamicUrl = ''; 
                    }
                    updateQR();
                });
                
                // Handle blur for auto-https insertion in the input field itself
                if (el === textInput) {
                    el.addEventListener('blur', () => {
                         if (currentType === 'url' && textInput.value.length > 0 && !textInput.value.startsWith('http')) {
                             textInput.value = "https://" + textInput.value;
                             updateQR();
                         }
                    });
                }
            });

            // Logo Handling
            logoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function() {
                        logoUrl = reader.result;
                        fileNameSpan.textContent = file.name;
                        clearLogoBtn.classList.remove('hidden');
                        updateQR();
                    }
                    reader.readAsDataURL(file);
                }
            });

            clearLogoBtn.addEventListener('click', function() {
                logoUrl = null;
                logoInput.value = '';
                fileNameSpan.textContent = "Pilih file logo...";
                clearLogoBtn.classList.add('hidden');
                updateQR();
            });

            // Download
            downloadBtn.addEventListener('click', () => {
                const size = parseInt(sizeInput.value);
                
                // Update size for download (with margin for clean output)
                qrCode.update({
                    width: size,
                    height: size,
                    margin: Math.round(size * 0.03) // 3% margin for download
                });

                qrCode.download({ 
                    name: "q-space-qr-" + Date.now(), 
                    extension: "png" 
                }).then(() => {
                    // Reset to preview size
                    qrCode.update({
                        width: 300,
                        height: 300,
                        margin: 10
                    });
                });
            });
        });
    </script>
</x-app-layout>
