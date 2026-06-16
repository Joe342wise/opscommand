<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'OpsCommand') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|jetbrains-mono:400" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .fill-icon {
            font-variation-settings: 'FILL' 1;
        }
        ::-webkit-scrollbar {
            width: 4px;
        }
        ::-webkit-scrollbar-track {
            background: #0b1326;
        }
        ::-webkit-scrollbar-thumb {
            background: #2d3449;
            border-radius: 10px;
        }
    </style>
</head>
<body class="bg-background text-on-surface font-body-md overflow-hidden selection:bg-primary/30">
    @include('layouts.sidebar')

    <header class="docked full-width top-0 sticky z-40 bg-surface border-b border-surface-variant/30 flex justify-between items-center h-14 px-6 ml-64">
        <div class="flex items-center gap-6">
            <div class="relative group">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant text-sm">search</span>
                <input type="text" placeholder="Global search..."
                       class="bg-surface-container-low border border-surface-variant/50 rounded-md pl-9 pr-4 py-1.5 text-body-sm focus:outline-none focus:ring-1 focus:ring-primary w-64 transition-all focus:w-80">
            </div>
            <div class="h-8 w-px bg-surface-variant mx-2"></div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-success-emerald animate-pulse"></span>
                    <span class="font-label-caps text-label-caps text-success-emerald font-bold">SYSTEMS ONLINE</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <a href="{{ route('notifications.index') }}" class="material-symbols-outlined text-on-surface-variant hover:text-on-surface transition-colors p-2 rounded-full hover:bg-surface-variant/30">notifications</a>
            <div class="h-6 w-px bg-surface-variant mx-2"></div>
            <div class="flex items-center gap-2">
                <span class="font-mono-data text-mono-data text-on-surface-variant bg-surface-container px-3 py-1 rounded border border-surface-variant/30"
                      x-data="{ time: '' }" x-init="setInterval(() => { const now = new Date(); time = 'UTC ' + now.getUTCHours().toString().padStart(2,'0') + ':' + now.getUTCMinutes().toString().padStart(2,'0') + ':' + now.getUTCSeconds().toString().padStart(2,'0'); }, 1000)" x-text="time">UTC 00:00:00</span>
            </div>
        </div>
    </header>

    <main class="ml-64 px-margin-page pt-8 pb-margin-page h-[calc(100vh-3.5rem)] overflow-y-auto">
        <div class="max-w-[1600px] mx-auto space-y-margin-page">
            {{ $slot }}
        </div>
    </main>

    <!-- Background Atmospheric Effect -->
    <div class="fixed inset-0 -z-10 pointer-events-none overflow-hidden">
        <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-primary/5 blur-[120px] rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-0 w-[500px] h-[500px] bg-danger-rose/5 blur-[120px] rounded-full translate-y-1/2 -translate-x-1/4"></div>
    </div>

    @livewireScripts
</body>
</html>
