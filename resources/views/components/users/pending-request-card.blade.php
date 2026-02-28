@props(['user'])

<div class="bg-white rounded-xl shadow-sm border border-amber-200/60 p-5 flex items-center justify-between gap-4">
    <div class="flex items-center gap-4 min-w-0">
        <div class="h-12 w-12 rounded-full bg-slate-100 flex items-center justify-center font-semibold text-slate-600 text-sm shrink-0">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div class="min-w-0">
            <p class="font-semibold text-gray-800">{{ $user->name }}</p>
            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
            <p class="text-xs text-slate-400 mt-0.5">Requested {{ $user->created_at->diffForHumans() }}</p>
        </div>
    </div>
    <form action="{{ route('users.approve', $user) }}" method="POST" class="shrink-0">
        @csrf
        <button type="submit"
                class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition-colors">
            Approve
        </button>
    </form>
</div>
