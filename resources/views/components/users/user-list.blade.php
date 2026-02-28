@props(['users'])

<div>
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-gray-800">All Users</h2>
        <span class="text-sm text-slate-500">{{ $users->count() }} {{ Str::plural('user', $users->count()) }}</span>
    </div>

    @if($users->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border-2 border-dashed border-gray-200 p-12 md:p-16 text-center">
            <div class="h-16 w-16 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <p class="text-gray-500 font-medium">No users found</p>
            <p class="text-sm text-gray-400 mt-1">Users will appear here once they register.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($users as $user)
                <x-users.user-card :user="$user" />
            @endforeach
        </div>
    @endif
</div>
