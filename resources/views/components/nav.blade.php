<aside class="w-64 shrink-0 h-full bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-slate-100 border-r border-slate-800/50 overflow-hidden">
    <nav class="h-full flex flex-col px-4 py-6">
        {{-- Nav section label --}}
        <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500 px-3 mb-3">Menu</p>

        {{-- Main nav links --}}
        <div class="space-y-1 font-medium">
            <a href="{{ route('home') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-all duration-200 {{ request()->routeIs('home') ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-slate-100' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('home') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('projects.my') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-all duration-200 {{ request()->routeIs('projects.my') ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-slate-100' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('projects.my') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
                <span>My Project</span>
            </a>
            <a href="{{ route('projects.add') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-all duration-200 {{ request()->routeIs('projects.add') ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-slate-100' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('projects.add') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                <span>Add Project</span>
            </a>
            <a href="{{ route('book-ticket') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-all duration-200 {{ request()->routeIs('book-ticket') ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-slate-100' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('book-ticket') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
                <span>Book Ticket</span>
            </a>
            <a href="{{ route('chat.index') }}"
               class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-all duration-200 {{ request()->routeIs('chat.*') ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-slate-100' }}">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('chat.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Chat</span>
            </a>

            @if(auth()->user()?->isSuperAdmin())
            <div class="pt-4 mt-4 border-t border-slate-800">
                <p class="text-[11px] font-medium uppercase tracking-wider text-slate-500 px-3 mb-3">Admin</p>
                <a href="{{ route('users.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-slate-800 text-white shadow-sm' : 'text-slate-300 hover:bg-slate-800/60 hover:text-slate-100' }}">
                    <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('users.*') ? 'text-amber-400' : 'text-slate-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span>User Management</span>
                </a>
            </div>
            @endif
        </div>

        {{-- Spacer --}}
        <div class="flex-1 min-h-[24px]"></div>

        {{-- Logout --}}
        <div class="pt-4 border-t border-slate-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 rounded-lg px-3 py-2.5 text-[15px] text-slate-300 hover:bg-red-500/10 hover:text-red-400 transition-all duration-200 group">
                    <svg class="w-5 h-5 shrink-0 text-slate-400 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>
