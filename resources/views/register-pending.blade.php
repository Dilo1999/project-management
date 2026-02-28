<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Account Pending Approval – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-slate-100 flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/60 p-8 text-center">
            <div class="h-14 w-14 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-6">
                <svg class="w-7 h-7 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-xl font-semibold text-slate-800">Account Pending Approval</h1>
            <p class="mt-3 text-sm text-slate-600 leading-relaxed">
                Your account has been created. An administrator must approve your request before you can log in. You will be able to sign in once your account is approved.
            </p>
            <a href="{{ route('login') }}"
               class="mt-6 inline-flex items-center gap-2 text-sm font-medium text-slate-700 hover:text-slate-900">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Sign in
            </a>
        </div>
        <p class="mt-6 text-center text-xs text-slate-400">{{ config('app.name') }}</p>
    </div>
</body>
</html>
