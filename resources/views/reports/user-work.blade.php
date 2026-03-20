<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('components.favicon')
    <title>User Work Report – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-slate-100">
    @include('components.header')

    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        @include('components.nav')

        <main class="flex-1 min-w-0 min-h-0 overflow-y-auto bg-gray-50">
            <div class="w-full p-6 md:p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">User Work Report</h1>
                    <p class="text-sm text-gray-500 mt-1">Click a user to view their projects and tasks — admin only</p>
                </div>

                <ul class="space-y-3">
                    @foreach($users as $u)
                        @php
                            $projectCount = $counts[$u->id]['projects'] ?? 0;
                            $ticketCount = $counts[$u->id]['tickets'] ?? 0;
                        @endphp
                        <li>
                            <a href="{{ route('reports.user-work.show', $u) }}"
                               class="flex items-center justify-between gap-4 rounded-xl bg-white px-5 py-4 shadow-sm border border-gray-100 hover:shadow-md hover:border-gray-200 transition-all duration-200 group">
                                <div class="min-w-0 flex-1">
                                    <p class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors">{{ $u->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $u->email }}</p>
                                </div>
                                <div class="flex items-center gap-4 shrink-0 text-sm text-gray-500">
                                    <span>{{ $projectCount }} {{ Str::plural('project', $projectCount) }}</span>
                                    <span>{{ $ticketCount }} {{ Str::plural('task', $ticketCount) }}</span>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </li>
                    @endforeach
                </ul>

                @if($users->isEmpty())
                    <p class="text-sm text-gray-500 italic">No approved users yet.</p>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
