<header class="bg-white/95 backdrop-blur-sm border-b border-slate-200/80 shadow-sm">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-3 sm:gap-4">
        {{-- Left: mobile menu button + logo --}}
        <div class="flex items-center gap-2 sm:gap-3 min-w-0">
            <button type="button"
                    class="inline-flex items-center justify-center rounded-lg p-2 text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white md:hidden"
                    aria-label="Open navigation menu"
                    aria-expanded="false"
                    data-sidebar-toggle>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <a href="{{ route('home') }}" class="flex items-center gap-2.5 group shrink-0 -ml-1 sm:-ml-3">
                <img src="{{ asset('images/logo/Logo---H.jpg.jpeg') }}" alt="Logo" class="max-h-10 sm:max-h-12 w-auto object-contain group-hover:opacity-90 transition-opacity shrink-0" />
            </a>
        </div>

        {{-- Right section: Messages + User --}}
        <div class="flex items-center gap-3 sm:gap-4 pl-1 sm:pl-0 min-w-0">
            <a href="{{ route('my-messages') }}"
               class="relative p-2 rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-800 transition-colors {{ request()->routeIs('my-messages') ? 'bg-slate-100 text-slate-800' : '' }}"
               title="My messages">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                @php
                    $unreadCount = \App\Models\Ticket::where('assigned_to_user_id', auth()->id())
                        ->whereNotIn('status', ['done', 'rejected'])
                        ->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -right-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-orange-500 text-[10px] font-bold text-white">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                @endif
            </a>
            <div class="h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center text-xs sm:text-sm font-semibold text-white shadow-sm shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-slate-800 truncate max-w-[110px] sm:max-w-[180px]">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="text-[10px] sm:text-xs text-slate-500">Logged in</p>
            </div>
        </div>
    </div>
</header>
