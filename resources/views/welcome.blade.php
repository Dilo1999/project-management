<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased min-h-screen flex justify-center items-center p-4 bg-[#e0e5ec]">
    <div class="w-[340px] bg-[#e0e5ec] rounded-[25px] shadow-neu-flat py-[50px] px-10 text-center">
        <div
            class="w-20 h-20 mx-auto mb-5 rounded-full bg-[#e0e5ec] shadow-neu-inset-sm overflow-hidden bg-cover bg-center"
            style="background-image: url('https://picsum.photos/200');"
        ></div>
        <h2 class="text-[#333] text-[1.3rem] font-semibold mb-1">Web Development</h2>
        <p class="text-[0.9rem] text-[#666] mb-6">Made easy!</p>

        @if ($errors->any())
            <div class="mb-4 p-3 rounded-2xl text-red-600 text-sm shadow-neu-inset bg-[#e0e5ec]">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="relative mb-5">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#666] text-lg" aria-hidden="true">👤</span>
                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email') }}"
                    placeholder="email"
                    autocomplete="email"
                    required
                    autofocus
                    class="w-full py-3 pl-12 pr-4 border-0 rounded-[15px] bg-[#e0e5ec] text-[#333] text-[0.95rem] outline-none shadow-neu-inset focus:ring-2 focus:ring-[#a3b1c6]/30"
                />
            </div>

            <div class="relative mb-5">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[#666] text-lg" aria-hidden="true">🔒</span>
                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="password"
                    autocomplete="current-password"
                    required
                    class="w-full py-3 pl-12 pr-4 border-0 rounded-[15px] bg-[#e0e5ec] text-[#333] text-[0.95rem] outline-none shadow-neu-inset focus:ring-2 focus:ring-[#a3b1c6]/30"
                />
            </div>

            <button
                type="submit"
                class="w-full py-3 px-4 border-0 rounded-[20px] bg-[#40a9c3] text-white text-base font-semibold cursor-pointer shadow-neu-btn hover:bg-[#3a97af] transition-colors"
            >
                Login
            </button>

            <div class="mt-4 text-[0.85rem] text-[#555]">
                Forgot password? <a href="{{ route('register') }}" class="text-[#111] no-underline font-semibold hover:underline">or Sign Up</a>
            </div>
        </form>
    </div>
</body>
</html>
