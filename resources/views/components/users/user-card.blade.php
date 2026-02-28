@props(['user'])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md hover:border-slate-200/80 transition-all duration-200 group">
    <div class="flex items-start gap-4">
        <div class="h-12 w-12 rounded-full flex items-center justify-center font-semibold text-sm shrink-0 {{ $user->role === 'super_admin' ? 'bg-gradient-to-br from-amber-500 to-orange-600 text-white shadow-md' : 'bg-slate-100 text-slate-600' }}">
            {{ strtoupper(substr($user->name, 0, 2)) }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="font-semibold text-gray-800 truncate group-hover:text-orange-600 transition-colors">{{ $user->name }}</p>
            <p class="text-sm text-gray-500 truncate mt-0.5">{{ $user->email }}</p>
            <span class="inline-flex mt-3 px-2.5 py-1 rounded-lg text-xs font-medium {{ $user->role === 'super_admin' ? 'bg-amber-100 text-amber-800 border border-amber-200/60' : 'bg-slate-100 text-slate-600 border border-slate-200/60' }}">
                {{ $user->role === 'super_admin' ? 'Super Admin' : 'User' }}
            </span>
        </div>
    </div>
</div>
