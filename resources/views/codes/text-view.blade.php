<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $qrText->title ?? 'Q-Space Text' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body { font-family: 'Figtree', sans-serif; }
        
        /* Theme: Default */
        .theme-default {
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        
        /* Theme: Dark */
        .theme-dark {
            --bg: #0f172a;
            --card-bg: #1e293b;
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --border: #334155;
        }
        
        /* Theme: Elegant */
        .theme-elegant {
            --bg: #faf5f0;
            --card-bg: #ffffff;
            --text: #292524;
            --text-muted: #78716c;
            --border: #e7e5e4;
        }
        
        /* Theme: Colorful */
        .theme-colorful {
            --bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --card-bg: #ffffff;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        
        .themed-bg {
            background: var(--bg);
            min-height: 100vh;
        }
        
        .themed-card {
            background: var(--card-bg);
            color: var(--text);
            border-color: var(--border);
        }
        
        .themed-text-muted {
            color: var(--text-muted);
        }
        
        /* Content Styling */
        .content-area h1 { font-size: 2em; font-weight: 700; margin: 0.67em 0; }
        .content-area h2 { font-size: 1.5em; font-weight: 600; margin: 0.75em 0; }
        .content-area h3 { font-size: 1.17em; font-weight: 600; margin: 0.83em 0; }
        .content-area p { margin: 1em 0; line-height: 1.7; }
        .content-area ul { list-style-type: disc; padding-left: 2em; margin: 1em 0; }
        .content-area ol { list-style-type: decimal; padding-left: 2em; margin: 1em 0; }
        .content-area li { margin: 0.5em 0; }
        .content-area strong, .content-area b { font-weight: 700; }
        .content-area em, .content-area i { font-style: italic; }
        .content-area u { text-decoration: underline; }
        .content-area s { text-decoration: line-through; }
        .content-area a { color: #3b82f6; text-decoration: underline; }
        .content-area blockquote { 
            border-left: 4px solid #e2e8f0; 
            padding-left: 1em; 
            margin: 1em 0;
            color: var(--text-muted);
            font-style: italic;
        }
        .content-area pre {
            background: #f1f5f9;
            padding: 1em;
            border-radius: 0.5em;
            overflow-x: auto;
            font-family: monospace;
        }
        .content-area code {
            background: #f1f5f9;
            padding: 0.2em 0.4em;
            border-radius: 0.25em;
            font-family: monospace;
            font-size: 0.9em;
        }
        .content-area img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5em;
        }
    </style>
</head>
<body class="theme-{{ $qrText->theme }} themed-bg">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Card -->
            <div class="themed-card rounded-3xl shadow-xl border p-8 md:p-12">
                @if($qrText->title)
                    <h1 class="text-3xl md:text-4xl font-bold mb-6">{{ $qrText->title }}</h1>
                    <hr class="border-current opacity-10 mb-8">
                @endif
                
                <!-- Content -->
                <div class="content-area text-lg leading-relaxed">
                    {!! $qrText->content !!}
                </div>
                
                <!-- Footer -->
                <div class="mt-12 pt-6 border-t border-current border-opacity-10">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <p class="themed-text-muted text-sm">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ number_format($qrText->views) }} views
                        </p>
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-sm font-semibold themed-text-muted hover:opacity-80 transition-opacity">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                            </svg>
                            Powered by Q-Space
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
