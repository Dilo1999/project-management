<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Messages – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-slate-100">
    @include('components.header')

    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        @include('components.nav')

        <main class="flex-1 min-w-0 min-h-0 overflow-y-auto p-8 bg-gray-50">
            <div class="max-w-3xl mx-auto">
                <div class="mb-6">
                    <h1 class="text-3xl font-semibold text-gray-800">My Messages</h1>
                    <p class="text-sm text-gray-500 mt-2">Emergency tasks assigned to you</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if($tickets->isEmpty())
                    <div class="bg-white rounded-xl shadow-md p-12 text-center">
                        <svg class="w-16 h-16 mx-auto text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                        </svg>
                        <p class="text-gray-500">No messages or tasks assigned to you yet.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($tickets as $ticket)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-semibold text-sm">
                                            {{ strtoupper(substr($ticket->assignedBy->name ?? 'U', 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $ticket->assignedBy->name ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $ticket->created_at->format('M j, Y \a\t g:i A') }}</p>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                        {{ $ticket->status === 'open' ? 'bg-amber-100 text-amber-800' : ($ticket->status === 'accepted' ? 'bg-green-100 text-green-800' : ($ticket->status === 'done' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </div>
                                <div class="text-gray-600 text-sm leading-relaxed whitespace-pre-wrap mb-4">{{ $ticket->description }}</div>
                                @if($ticket->status === 'done' && $ticket->note)
                                    <div class="text-gray-500 text-sm mb-4 p-3 bg-slate-50 rounded-lg border border-slate-100">
                                        <span class="font-medium text-slate-600">Note:</span> {{ $ticket->note }}
                                    </div>
                                @endif
                                @if($ticket->status === 'open')
                                    <div class="flex gap-3 pt-2 border-t border-gray-100">
                                        <form action="{{ route('tickets.accept', $ticket) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors text-sm font-medium">
                                                Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('tickets.reject', $ticket) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors text-sm font-medium">
                                                Cancel
                                            </button>
                                        </form>
                                    </div>
                                @elseif($ticket->status === 'accepted')
                                    <div class="pt-2 border-t border-gray-100">
                                        <form action="{{ route('tickets.markDone', $ticket) }}" method="POST" class="space-y-3">
                                            @csrf
                                            <label for="note-{{ $ticket->id }}" class="block text-sm font-medium text-gray-700">Add note and mark task as done</label>
                                            <textarea name="note" id="note-{{ $ticket->id }}" rows="3" required
                                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                                placeholder="Enter your completion note..."></textarea>
                                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors text-sm font-medium">
                                                Add Note & Mark Done
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </main>
    </div>
</body>
</html>
