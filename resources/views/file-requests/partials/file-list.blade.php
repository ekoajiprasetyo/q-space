@props(['files', 'compact' => false])

<!-- Student Notes (Bubble) -->
@if($files->first()->student_notes)
    <div class="relative bg-white p-4 rounded-xl rounded-tl-none border border-slate-200 shadow-sm mb-6 {{ $compact ? 'text-xs' : '' }}">
        <div class="absolute -top-[9px] -left-[1px] w-3 h-3 bg-white border-t border-r border-slate-200 transform -scale-x-100 rotate-45 skew-x-12"></div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Catatan Siswa</p>
        <p class="text-slate-700 leading-relaxed">{{ $files->first()->student_notes }}</p>
    </div>
@endif

<!-- Files Grid -->
<div class="grid grid-cols-1 {{ $compact ? 'gap-2' : 'sm:grid-cols-2 lg:grid-cols-3 gap-3' }}">
    @foreach($files as $file)
        <a href="{{ $file->google_drive_url }}" target="_blank" class="group/card relative flex items-center gap-3 p-3 bg-white border border-slate-200 rounded-xl hover:border-indigo-300 hover:ring-2 hover:ring-indigo-100 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
            
            <!-- File Icon -->
            <div class="{{ $compact ? 'w-8 h-8' : 'w-10 h-10' }} rounded-lg flex items-center justify-center shrink-0 
                @if(Str::contains($file->mime_type, 'image')) bg-purple-50 text-purple-500
                @elseif(Str::contains($file->mime_type, 'pdf')) bg-red-50 text-red-500
                @elseif(Str::contains($file->mime_type, 'spreadsheet') || Str::contains($file->mime_type, 'excel')) bg-green-50 text-green-500
                @elseif(Str::contains($file->mime_type, 'word') || Str::contains($file->mime_type, 'document')) bg-blue-50 text-blue-500
                @elseif(Str::contains($file->mime_type, 'presentation') || Str::contains($file->mime_type, 'powerpoint')) bg-orange-50 text-orange-500
                @else bg-slate-50 text-slate-500 @endif">
                
                @if(Str::contains($file->mime_type, 'image'))
                    <svg class="{{ $compact ? 'w-4 h-4' : 'w-5 h-5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                @elseif(Str::contains($file->mime_type, 'pdf'))
                    <svg class="{{ $compact ? 'w-4 h-4' : 'w-5 h-5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                @else
                    <svg class="{{ $compact ? 'w-4 h-4' : 'w-5 h-5' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                @endif
            </div>

            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-700 truncate group-hover/card:text-indigo-600 transition-colors">{{ $file->original_filename }}</p>
                <p class="text-[10px] font-semibold text-slate-400 uppercase mt-0.5">{{ number_format($file->file_size / 1024, 0) }} KB</p>
            </div>

            <div class="opacity-0 group-hover/card:opacity-100 transition-opacity absolute right-3 top-3 bg-white p-1 rounded-md shadow-sm border border-slate-100">
                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </div>
        </a>
    @endforeach
</div>
