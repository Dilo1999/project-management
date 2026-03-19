<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $user->name }} – User Work Report – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-slate-100">
    @include('components.header')

    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        @include('components.nav')

        <main class="flex-1 min-w-0 min-h-0 overflow-y-auto bg-gradient-to-b from-slate-50 to-gray-100">
            <div class="w-full max-w-4xl mx-auto p-6 md:p-8">
                {{-- Back link --}}
                <a href="{{ route('reports.user-work') }}"
                   class="inline-flex items-center gap-2 text-sm text-slate-500 hover:text-amber-600 font-medium mb-6 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to User Work Report
                </a>

                {{-- User header card --}}
                <div class="rounded-2xl bg-white shadow-sm border border-slate-200/80 overflow-hidden mb-8">
                    <div class="bg-gradient-to-br from-amber-500/10 via-orange-500/5 to-transparent px-6 md:px-8 py-6">
                        <div class="flex items-center gap-4">
                            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 text-lg font-bold text-white shadow-lg shadow-amber-500/25">
                                {{ strtoupper(mb_substr($user->name, 0, 2)) }}
                            </div>
                            <div class="min-w-0">
                                <h1 class="text-xl md:text-2xl font-bold text-slate-800 tracking-tight">{{ $user->name }}</h1>
                                <p class="text-sm text-slate-500 mt-0.5 flex items-center gap-1.5">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $user->email }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    {{-- Projects --}}
                    <section class="rounded-2xl bg-white shadow-sm border border-slate-200/80 overflow-hidden">
                        <div class="px-6 py-4 bg-slate-50/90 border-b border-slate-100 flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-500/15 text-emerald-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Projects</h2>
                                <p class="text-xs text-slate-500">{{ $projects->count() }} {{ Str::plural('project', $projects->count()) }}</p>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($projects->isEmpty())
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-500">No projects assigned</p>
                                </div>
                            @else
                                <ul class="space-y-5">
                                    @foreach($projects as $project)
                                        @php
                                            $dur = $projectDurations[$project->id] ?? ['days' => 0, 'hours' => 0, 'minutes' => 0];
                                            $durParts = [];
                                            if ($dur['days'] > 0) $durParts[] = $dur['days'] . ' ' . Str::plural('day', $dur['days']);
                                            if ($dur['hours'] > 0) $durParts[] = $dur['hours'] . ' ' . Str::plural('hour', $dur['hours']);
                                            if ($dur['minutes'] > 0 && $dur['days'] === 0) $durParts[] = $dur['minutes'] . ' ' . Str::plural('min', $dur['minutes']);
                                            $durationLabel = count($durParts) ? implode(', ', $durParts) : '—';
                                            $statusColor = \App\Models\Project::STATUS_COLORS[$project->status] ?? '#94a3b8';
                                        @endphp
                                        <li class="group rounded-xl border border-slate-100 bg-slate-50/50 p-4 transition-shadow hover:shadow-md hover:border-slate-200/80">
                                            <div class="flex flex-wrap items-start justify-between gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <h3 class="font-semibold text-slate-800">{{ $project->name }}</h3>
                                                    <p class="text-xs text-slate-500 mt-1">
                                                        {{ $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('M j, Y') : '' }}
                                                        – {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('M j, Y') : '' }}
                                                    </p>
                                                </div>
                                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold border border-slate-200/80 text-slate-700 shrink-0"
                                                      style="background-color: {{ $statusColor }}20; color: {{ $statusColor }}; border-color: {{ $statusColor }}40;">
                                                    {{ $project->status }}
                                                </span>
                                            </div>
                                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs">
                                                <span class="inline-flex items-center gap-1 rounded-md bg-amber-500/10 px-2 py-1 text-amber-700 font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $durationLabel }}
                                                </span>
                                            </div>
                                            @if($project->description)
                                                <p class="text-sm text-slate-600 mt-3 leading-relaxed">{{ $project->description }}</p>
                                            @else
                                                <p class="text-sm text-slate-400 italic mt-3">No description.</p>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </section>

                    {{-- Tasks (tickets) --}}
                    <section class="rounded-2xl bg-white shadow-sm border border-slate-200/80 overflow-hidden">
                        <div class="px-6 py-4 bg-slate-50/90 border-b border-slate-100 flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-500/15 text-amber-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-sm font-semibold text-slate-700 uppercase tracking-wider">Tasks (tickets)</h2>
                                <p class="text-xs text-slate-500">{{ $tickets->count() }} {{ Str::plural('task', $tickets->count()) }}</p>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($tickets->isEmpty())
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400 mb-3">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-500">No tasks assigned</p>
                                </div>
                            @else
                                <ul class="space-y-5">
                                    @foreach($tickets as $ticket)
                                        @php
                                            $td = $ticketDurations[$ticket->id] ?? ['days' => 0, 'hours' => 0, 'minutes' => 0];
                                            $tParts = [];
                                            if ($td['days'] > 0) $tParts[] = $td['days'] . ' ' . Str::plural('day', $td['days']);
                                            if ($td['hours'] > 0) $tParts[] = $td['hours'] . ' ' . Str::plural('hour', $td['hours']);
                                            if ($td['minutes'] > 0 || count($tParts) === 0) {
                                                $tParts[] = $td['minutes'] . ' ' . Str::plural('min', $td['minutes']);
                                            }
                                            $taskDurationLabel = implode(', ', $tParts);
                                        @endphp
                                        <li class="group rounded-xl border border-amber-100 bg-amber-50/30 p-4 transition-shadow hover:shadow-md hover:border-amber-200/80">
                                            <div class="flex flex-wrap items-start justify-between gap-2">
                                                @if($ticket->assignedBy)
                                                    <p class="text-xs text-slate-500">
                                                        From <span class="font-medium text-slate-600">{{ $ticket->assignedBy->name }}</span>
                                                        @if($ticket->created_at)
                                                            · {{ \Carbon\Carbon::parse($ticket->created_at)->format('M j, Y H:i') }}
                                                        @endif
                                                    </p>
                                                @endif
                                                <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-semibold
                                                    {{ $ticket->status === 'done' ? 'bg-emerald-500/15 text-emerald-700 border border-emerald-200/60' : 'bg-slate-100 text-slate-600 border border-slate-200/80' }}">
                                                    {{ $ticket->status }}
                                                </span>
                                            </div>
                                            <p class="text-sm text-slate-800 mt-2 font-medium leading-snug">{{ $ticket->description }}</p>
                                            <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                                                <span class="inline-flex items-center gap-1 rounded-md bg-slate-100 px-2 py-1 text-slate-600 font-medium">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $taskDurationLabel }}
                                                </span>
                                                @if($ticket->done_at)
                                                    <span>Done {{ \Carbon\Carbon::parse($ticket->done_at)->format('M j, Y H:i') }}</span>
                                                @endif
                                            </div>
                                            @if($ticket->note)
                                                <div class="mt-3 rounded-lg bg-slate-50 border border-slate-100 px-3 py-2">
                                                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wide">Note</p>
                                                    <p class="text-sm text-slate-600 mt-0.5">{{ $ticket->note }}</p>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
