@props(['user'])

@php
    $isSelf = auth()->id() === $user->id;
    $roleLabel = match ($user->role) {
        'super_admin' => 'Super Admin',
        'developer' => 'Developer',
        'designer' => 'Designer',
        default => 'Normal',
    };
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-slate-200/80 transition-all duration-200 group flex flex-col">
    <div class="flex items-start gap-4 flex-1">
        <div class="h-12 w-12 rounded-full flex items-center justify-center font-semibold text-sm shrink-0 {{ $user->role === 'super_admin' ? 'bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md' : 'bg-slate-100 text-slate-600' }}">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-semibold text-gray-800 truncate group-hover:text-orange-600 transition-colors">{{ $user->name }}</p>
            <p class="text-sm text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
            <span class="inline-flex mt-3 px-2.5 py-1 rounded-lg text-xs font-medium {{ $user->role === 'super_admin' ? 'bg-amber-100 text-amber-800 border border-amber-200/60' : 'bg-slate-100 text-slate-600 border border-slate-200/60' }}">
                {{ $roleLabel }}
            </span>
        </div>
    </div>

    @if(!$isSelf && $user->role !== 'super_admin')
        <form action="{{ route('users.role', $user) }}" method="POST" class="mt-4 pt-4 border-t border-gray-100">
            @csrf
            @method('PATCH')
            <div class="flex items-center gap-2">
                <select name="role"
                        class="flex-1 px-3 py-2 rounded-lg border border-slate-200 bg-white text-sm text-slate-700">
                    <option value="developer" @selected($user->role === 'developer')>Developer</option>
                    <option value="designer" @selected($user->role === 'designer')>Designer</option>
                    <option value="normal" @selected($user->role === 'normal' || $user->role === 'user')>Normal</option>
                </select>
                <button type="submit"
                        class="px-3 py-2 text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 rounded-lg transition-colors">
                    Save
                </button>
            </div>
        </form>
    @endif

    @unless($isSelf)
        <form action="{{ route('users.destroy', $user) }}" method="POST" class="mt-3"
              onsubmit="return confirm('Remove {{ e($user->name) }}? Their projects and tickets linked to them will be deleted.');">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="w-full px-3 py-2 text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 rounded-lg transition-colors">
                Remove user
            </button>
        </form>
    @endunless
</div>
