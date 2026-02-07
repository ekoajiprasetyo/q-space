<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' | ' : '' }}{{ config('app.name', 'Q-Space') }}</title>
        <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            html, body {
                width: 100%;
                overflow-x: hidden;
                position: relative;
            }
            * {
                -webkit-tap-highlight-color: transparent !important;
                outline: none !important;
            }
            *:focus {
                outline: none !important;
                box-shadow: none !important;
            }
        </style>
    </head>
    <body class="font-sans antialiased w-full relative">
        <div class="min-h-screen bg-gray-50 pt-32 pb-32 md:pb-0">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

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
                        <p class="font-bold text-sm" x-text="notification.type === 'success' ? 'Berhasil!' : 'Terjadi Kesalahan'"></p>
                        <p class="text-sm opacity-90 leading-tight mt-1" x-text="notification.message"></p>
                    </div>
                </div>
            </template>
        </div>
    </body>
</html>
