<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('components.favicon')
    <title>User Management – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-slate-100">
    @include('components.header')

    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        @include('components.nav')

        <main class="flex-1 min-w-0 min-h-0 overflow-y-auto bg-gray-50">
            <div class="w-full p-6 md:p-8">
                <x-users.page-header
                    title="User Management"
                    subtitle="Manage and overview all users in the system"
                />

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700 text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <x-users.pending-requests :pending-users="$pendingUsers" />

                <x-users.stats-grid
                    :total-users="$stats['totalUsers']"
                    :super-admins="$stats['superAdmins']"
                    :pending-count="$stats['pendingCount']"
                />

                <x-users.user-list :users="$users" />
            </div>
        </main>
    </div>
</body>
</html>
