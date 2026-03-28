<x-app-layout>
    <div x-data="{ 
        view: localStorage.getItem('file_request_view_mode') || 'list',
        deleteSubmissionModalOpen: false,
        deleteSubmitterName: '',
        deleteActionUrl: '', 
        queueRunnerUrl: @js($queueRunnerUrl ?? null),
        setView(val) { 
            this.view = val; 
            localStorage.setItem('file_request_view_mode', val); 
        },
        async runQueueOnce() {
            if (!this.queueRunnerUrl) return;
            try {
                await fetch(this.queueRunnerUrl, { method: 'GET', keepalive: true, credentials: 'same-origin' });
            } catch (_) {}
        },
        confirmDeleteSubmission(url, name) {
            this.deleteActionUrl = url;
            this.deleteSubmitterName = name;
            this.deleteSubmissionModalOpen = true;
        }
    }" x-init="setTimeout(() => runQueueOnce(), 500)"
    class="pt-4 pb-20 max-w-7xl mx-auto sm:px-6 lg:px-8 relative isolate">
        
        <!-- Background Decor (Dashboard Style) -->
        <div class="absolute -top-20 -left-20 w-[500px] h-[500px] bg-blue-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>
        <div class="absolute -top-20 -right-20 w-[500px] h-[500px] bg-purple-200/60 rounded-full blur-[80px] -z-10 pointer-events-none mix-blend-multiply"></div>

        <!-- Header & Controls -->
        <div class="flex flex-col gap-6 mb-8 px-4 sm:px-0">
            <!-- Breadcrumb & Title -->
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div class="flex-1">
                    <a href="{{ route('files.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors mb-4 group">
                        <div class="w-6 h-6 rounded-full bg-white border border-slate-200 flex items-center justify-center group-hover:bg-slate-100 transition-colors">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </div>
                        Kembali ke Daftar
                    </a>
                    
                    <h2 class="text-3xl font-black text-slate-800 tracking-tight leading-tight mb-2">{{ $fileRequest->title }}</h2>
                    
                    <div class="flex items-center gap-2 text-sm font-medium text-slate-500">
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-600 border border-blue-100 text-xs font-bold">
                            {{ $submissions->count() }} Siswa
                        </span>
                        <span class="text-slate-300">/</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200 text-xs font-bold">
                            {{ $fileRequest->submissions->count() }} File Total
                        </span>
                        <span class="text-slate-300">/</span>
                        <span class="flex items-center gap-1.5 {{ $fileRequest->is_active ? 'text-teal-600' : 'text-slate-400' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $fileRequest->is_active ? 'bg-teal-500' : 'bg-slate-300' }}"></span>
                            {{ $fileRequest->is_active ? 'Aktif' : 'Non-Aktif' }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Google Drive Button -->
                    <a href="{{ $fileRequest->google_drive_folder_url }}" target="_blank" 
                       class="flex items-center gap-2 px-5 py-2.5 bg-white text-slate-700 font-bold text-sm rounded-xl border border-slate-200 shadow-sm hover:shadow-md hover:border-blue-200 hover:text-blue-600 transition-all">
                        <svg class="w-5 h-5 opacity-75" viewBox="0 0 87.3 78"><path d="m6.6 66.85 3.85 6.65c.8 1.4 1.95 2.5 3.3 3.3l13.75-23.8h-27.5c0 1.55.4 3.1 1.2 4.5z" fill="#0066da"/><path d="m43.65 25-13.75-23.8c-1.35.8-2.5 1.9-3.3 3.3l-25.4 44a9.06 9.06 0 0 0 -1.2 4.5h27.5z" fill="#00ac47"/><path d="m73.55 76.8c1.35-.8 2.5-1.9 3.3-3.3l1.6-2.75 7.65-13.25c.8-1.4 1.2-2.85 1.2-4.5h-27.502l5.852 11.5z" fill="#ea4335"/><path d="m43.65 25 13.75-23.8c-1.35-.8-2.9-1.2-4.5-1.2h-18.5c-1.6 0-3.15.45-4.5 1.2z" fill="#00832d"/><path d="m59.8 53h-27.5l-13.75 23.8c1.35.8 2.9 1.2 4.5 1.2h50.5c1.6 0 3.15-.45 4.5-1.2z" fill="#2684fc"/><path d="m73.4 26.5-12.7-22c-.8-1.4-1.95-2.5-3.3-3.3l-13.75 23.8 29.75 1.5z" fill="#ffba00"/></svg>
                        Folder Drive
                    </a>
                </div>
            </div>

            <!-- Toolbar: Search & View Toggle -->
            <div class="flex flex-col sm:flex-row gap-4 justify-between items-center bg-white p-2 rounded-2xl border border-slate-200 shadow-sm">
                <!-- Search -->
                <form action="{{ route('file-requests.show', $fileRequest->id) }}" method="GET" class="w-full sm:w-96 relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." 
                           class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border-transparent focus:bg-white focus:border-blue-500 focus:ring-0 rounded-xl text-sm font-medium transition-all placeholder-slate-400">
                </form>

                <!-- View Toggle -->
                <div class="flex bg-slate-100 p-1 rounded-xl ml-auto">
                    <button @click="setView('grid')" :class="view === 'grid' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="p-2 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button @click="setView('list')" :class="view === 'list' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-700'" class="p-2 rounded-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        @php
            $pendingCount = (int) ($uploadTaskSummary->pending_count ?? 0);
            $failedCount = (int) ($uploadTaskSummary->failed_count ?? 0);
        @endphp
        @if($pendingCount > 0 || $failedCount > 0)
            <div class="mb-6 mx-4 sm:mx-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 flex flex-wrap gap-3 items-center">
                @if($pendingCount > 0)
                    <span class="px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold">
                        {{ $pendingCount }} file sedang diproses ke Google Drive
                    </span>
                @endif
                @if($failedCount > 0)
                    <span class="px-3 py-1 rounded-full bg-red-50 border border-red-200 text-red-700 text-xs font-bold">
                        {{ $failedCount }} file gagal upload - perlu retry
                    </span>
                @endif
            </div>
        @endif

        @if(isset($orphanUploadTasks) && $orphanUploadTasks->count() > 0)
            <div class="mb-6 mx-4 sm:mx-0 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">
                <p class="text-sm font-bold text-slate-700 mb-3">Upload Pending/Gagal (belum masuk daftar submission)</p>
                <div class="space-y-2">
                    @foreach($orphanUploadTasks as $task)
                        <div class="flex flex-wrap items-center justify-between gap-3 border border-slate-100 rounded-xl px-3 py-2">
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-slate-700 truncate">{{ $task->submitter_name }} - {{ $task->original_filename }}</p>
                                <p class="text-[11px] text-slate-500">
                                    Status:
                                    <span class="font-bold {{ $task->status === 'failed' ? 'text-red-600' : 'text-amber-600' }}">
                                        {{ strtoupper($task->status) }}
                                    </span>
                                    @if($task->last_error)
                                        - {{ \Illuminate\Support\Str::limit($task->last_error, 140) }}
                                    @endif
                                </p>
                            </div>
                            @if($task->status === 'failed')
                                <form action="{{ route('file-requests.upload-tasks.retry', [$fileRequest, $task]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 rounded-lg bg-red-600 text-white text-[11px] font-bold hover:bg-red-500 transition-colors">
                                        Retry
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if($submissions->isEmpty())
             <!-- Empty State -->
            <div class="bg-white rounded-[2rem] border border-slate-200 p-12 text-center shadow-sm mx-4 sm:mx-0">
                <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-xl font-bold text-slate-800 mb-2">Belum ada pengumpulan</h3>
                @if(request('search'))
                    <p class="text-slate-500">Tidak ditemukan siswa dengan nama matching "{{ request('search') }}".</p>
                    <a href="{{ route('file-requests.show', $fileRequest->id) }}" class="inline-block mt-4 text-blue-600 font-bold hover:underline">Reset Pencarian</a>
                @else
                    <p class="text-slate-500">File siswa akan muncul di sini setelah diupload.</p>
                @endif
            </div>
        @else
            <!-- Content Area -->
            <div class="mx-4 sm:mx-0">
                
                <!-- List View -->
                <div x-show="view === 'list'" class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="divide-y divide-slate-100">
                        @foreach($submissions as $submitter => $files)
                            @php
                                $tasksForSubmitter = $uploadTasks[$submitter] ?? collect();
                                $pendingTasksForSubmitter = $tasksForSubmitter->whereIn('status', ['queued', 'processing']);
                                $failedTasksForSubmitter = $tasksForSubmitter->where('status', 'failed');
                            @endphp
                            <div x-data="{ open: false }" class="group bg-white hover:bg-slate-50/80 transition-colors duration-200">
                                <!-- Main Row -->
                                <div @click="open = !open" class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 cursor-pointer">
                                    
                                    <!-- User Info -->
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-lg shadow-lg shadow-blue-500/20 shrink-0">
                                            {{ substr($submitter, 0, 1) }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-bold text-slate-800 text-base truncate">{{ $submitter }}</h3>
                                                @php
                                                    $isLate = false;
                                                    if ($fileRequest->deadline) {
                                                        $submissionJakarta = $files->first()->submitted_at->copy()->setTimezone('Asia/Jakarta');
                                                        // Compare as strings to handle potential naive storage (UTC stored as literal Jakarta time)
                                                        if ($submissionJakarta->format('Y-m-d H:i:s') > $fileRequest->deadline->format('Y-m-d H:i:s')) {
                                                            $isLate = true;
                                                        }
                                                    }
                                                @endphp
                                                @if($isLate)
                                                    <span class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100 uppercase tracking-wide">
                                                        Terlambat
                                                    </span>
                                                @endif
                                            </div>
                                            <p class="text-xs font-medium text-slate-400 flex items-center gap-1.5 mt-0.5">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ $files->first()->submitted_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}
                                                <span class="text-slate-300">&bull;</span>
                                                <span class="text-slate-500">{{ $files->first()->submitted_at->diffForHumans() }}</span>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Meta & Toggle -->
                                    <div class="flex items-center justify-between sm:justify-end gap-6 pl-16 sm:pl-0">
                                        <div class="flex items-center gap-2">
                                            <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-xs font-bold border border-indigo-100">
                                                {{ $files->count() }} File
                                            </span>
                                            @if($pendingTasksForSubmitter->count() > 0)
                                                <span class="bg-amber-50 text-amber-700 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-amber-200">
                                                    {{ $pendingTasksForSubmitter->count() }} Processing
                                                </span>
                                            @endif
                                            @if($failedTasksForSubmitter->count() > 0)
                                                <span class="bg-red-50 text-red-700 px-2.5 py-1 rounded-lg text-[11px] font-bold border border-red-200">
                                                    {{ $failedTasksForSubmitter->count() }} Failed
                                                </span>
                                            @endif
                                            
                                            <button @click.stop="confirmDeleteSubmission('{{ route('file-requests.submissions.destroy', $fileRequest) }}', '{{ $submitter }}')" 
                                                class="w-8 h-8 rounded-full bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all duration-300 ml-2" title="Hapus Semua File Siswa Ini">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                        
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center group-hover:bg-white group-hover:shadow-md group-hover:text-blue-500 transition-all duration-300 transform" 
                                             :class="open ? 'rotate-180 bg-blue-50 text-blue-600' : ''">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Expanded Content area -->
                                <div x-show="open" x-collapse style="display: none;">
                                    <div class="bg-indigo-50/30 border-t border-indigo-50/50 p-5 sm:pl-20 sm:pr-8">
                                        @if($failedTasksForSubmitter->count() > 0)
                                            <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200">
                                                <p class="text-xs font-bold text-red-700 mb-2">Upload Gagal (perlu retry):</p>
                                                <div class="space-y-2">
                                                    @foreach($failedTasksForSubmitter as $failedTask)
                                                        <form action="{{ route('file-requests.upload-tasks.retry', [$fileRequest, $failedTask]) }}" method="POST" class="flex items-center justify-between gap-3 bg-white border border-red-100 rounded-lg px-3 py-2">
                                                            @csrf
                                                            <div class="min-w-0">
                                                                <p class="text-xs font-bold text-slate-700 truncate">{{ $failedTask->original_filename }}</p>
                                                                <p class="text-[11px] text-red-600 truncate">{{ $failedTask->last_error ?: 'Gagal upload ke Google Drive' }}</p>
                                                            </div>
                                                            <button type="submit" class="shrink-0 px-3 py-1.5 rounded-lg bg-red-600 text-white text-[11px] font-bold hover:bg-red-500 transition-colors">
                                                                Retry
                                                            </button>
                                                        </form>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Shared File List Component -->
                                        @include('file-requests.partials.file-list', ['files' => $files])
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Grid View -->
                <div x-show="view === 'grid'" style="display: none;" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($submissions as $submitter => $files)
                        <div class="bg-white p-6 rounded-[2rem] border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col h-full">
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-lg shadow-blue-500/20 shrink-0">
                                    {{ substr($submitter, 0, 1) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <h3 class="font-bold text-slate-800 text-lg truncate" title="{{ $submitter }}">{{ $submitter }}</h3>
                                        @php
                                            $isLateGrid = false;
                                            if ($fileRequest->deadline) {
                                                $submissionJakartaGrid = $files->first()->submitted_at->copy()->setTimezone('Asia/Jakarta');
                                                if ($submissionJakartaGrid->format('Y-m-d H:i:s') > $fileRequest->deadline->format('Y-m-d H:i:s')) {
                                                    $isLateGrid = true;
                                                }
                                            }
                                        @endphp
                                        @if($isLateGrid)
                                            <span class="shrink-0 inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-600 border border-red-100 uppercase tracking-wide">
                                                Terlambat
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-0.5">
                                        <p class="text-xs font-bold text-slate-500">{{ $files->first()->submitted_at->setTimezone('Asia/Jakarta')->translatedFormat('d M Y, H:i') }}</p>
                                        <p class="text-[10px] font-medium text-slate-400">{{ $files->first()->submitted_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                            
                                <div class="flex-1">
                                    @include('file-requests.partials.file-list', ['files' => $files, 'compact' => true])
                                </div>

                                <div class="mt-4 pt-4 border-t border-slate-100 flex justify-end">
                                    <button @click="confirmDeleteSubmission('{{ route('file-requests.submissions.destroy', $fileRequest) }}', '{{ $submitter }}')" 
                                        class="flex items-center gap-2 text-red-400 hover:text-red-600 font-bold text-xs transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus Folder Siswa
                                    </button>
                                </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $distinctSubmitters->links() }}
                </div>
            </div>
        @endif
        
        <!-- Delete Submission Modal -->
        <template x-teleport="body">
            <div x-show="deleteSubmissionModalOpen" class="relative z-[999]" style="display: none;">
                <div x-show="deleteSubmissionModalOpen" 
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
                        <div x-show="deleteSubmissionModalOpen" 
                             @click.away="deleteSubmissionModalOpen = false" 
                             class="relative transform overflow-hidden rounded-[2rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100">
                            
                            <div class="bg-white px-4 pb-4 pt-5 sm:p-8 sm:pb-6 relative overflow-hidden">
                                <!-- Background decoration -->
                                <div class="absolute -top-10 -right-10 w-32 h-32 bg-red-50 rounded-full blur-2xl"></div>

                                <div class="sm:flex sm:items-start relative z-10">
                                    <div class="mx-auto flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-red-50 sm:mx-0 sm:h-12 sm:w-12 border border-red-100 shadow-sm ring-4 ring-red-50/50">
                                        <svg class="h-6 w-6 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                    </div>
                                    <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                        <h3 class="text-xl font-bold leading-6 text-slate-900">Hapus Folder Siswa?</h3>
                                        <div class="mt-3">
                                            <p class="text-slate-500 text-sm leading-relaxed">
                                                Anda akan menghapus semua file milik <span class="font-bold text-slate-800" x-text="deleteSubmitterName"></span>.
                                            </p>
                                            <div class="mt-4 p-3 bg-red-50/50 rounded-2xl border border-red-100 text-xs text-slate-600 flex gap-3 items-start text-left">
                                                <svg class="w-4 h-4 text-red-500 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                <p>File di Google Drive juga akan <span class="font-bold text-red-600">terhapus permanen</span>.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-gray-50/50 px-4 py-4 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                                <form :action="deleteActionUrl" method="POST" class="w-full sm:w-auto">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="submitter_name" :value="deleteSubmitterName">
                                    <button type="submit" class="inline-flex w-full justify-center rounded-full bg-red-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-red-500/30 hover:bg-red-500 hover:scale-[1.02] transition-all sm:w-auto">
                                        Hapus Permanen
                                    </button>
                                </form>
                                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-full bg-white px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-inset ring-slate-200 hover:bg-slate-50 sm:mt-0 sm:w-auto transition-all" @click="deleteSubmissionModalOpen = false">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
