@props(['pendingUsers'])

@if($pendingUsers->isNotEmpty())
    <div class="mb-8">
        <h2 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-amber-100 text-amber-600 text-xs font-bold">{{ $pendingUsers->count() }}</span>
            New Account Requests
        </h2>
        <div class="space-y-3">
            @foreach($pendingUsers as $user)
                <x-users.pending-request-card :user="$user" />
            @endforeach
        </div>
    </div>
@endif
