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
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0">
        <form action="{{ route('users.approve', $user) }}" method="POST">
            @csrf
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <select name="role"
                        required
                        class="w-full sm:w-auto px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm text-slate-700">
                    <option value="developer">Developer</option>
                    <option value="designer">Designer</option>
                    <option value="normal" selected>Normal</option>
                </select>
            <button type="submit"
                    class="w-full sm:w-auto px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-medium transition-colors">
                Approve
            </button>
            </div>
        </form>
        <form action="{{ route('users.destroy', $user) }}" method="POST"
              onsubmit="return confirm('Reject and remove this account request for {{ e($user->name) }}?');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-full sm:w-auto px-4 py-2 text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg text-sm font-medium transition-colors">
                Remove
            </button>
        </form>
    </div>
</div>
