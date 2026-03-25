<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('components.favicon')
    <title>Add Project – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-slate-100">
    @include('components.header')

    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        @include('components.nav')

        <main class="flex-1 min-w-0 min-h-0 overflow-y-auto p-8 bg-gray-50">
            <div class="max-w-3xl mx-auto">
                <div class="mb-6">
                    <h1 class="text-3xl font-semibold text-gray-800">Add New Project</h1>
                    <p class="text-sm text-gray-500 mt-2">Fill in the project details below</p>
                </div>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="bg-white rounded-lg shadow-lg p-8">
                    <form action="{{ route('projects.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Project Name</label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none"
                                   placeholder="Enter project name">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                                <input type="date" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                                <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none">
                            </div>
                        </div>

                        @php
                            $statusTextColors = ['New' => '#555', 'On' => '#065f46', 'Pause' => '#7c2d12', 'Waiting For Approval' => '#f5f3ff', 'Completed' => '#f0fdf4', 'Ticked Task' => '#eff6ff'];
                            $selectedStatus = old('status', 'New');
                        @endphp
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Status</label>
                            <div class="flex flex-wrap gap-3" role="radiogroup" aria-label="Status">
                                @foreach(\App\Models\Project::STATUS_OPTIONS as $option)
                                    @php
                                        $isSelected = $selectedStatus === $option;
                                        $bgColor = \App\Models\Project::STATUS_COLORS[$option];
                                        $textColor = $statusTextColors[$option] ?? '#374151';
                                    @endphp
                                    <label class="relative flex items-center gap-2 cursor-pointer rounded-full focus-within:ring-2 focus-within:ring-amber-500 focus-within:ring-offset-2 focus-within:outline-none"
                                           style="--status-bg: {{ $bgColor }}; --status-text: {{ $textColor }};">
                                        <input type="radio" name="status" value="{{ $option }}"
                                               {{ $isSelected ? 'checked' : '' }}
                                               class="peer absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                        <span class="flex items-center gap-2 px-3 py-1.5 rounded-full border-2 transition-all duration-150 text-sm font-medium select-none pointer-events-none border-[var(--status-bg)] bg-transparent text-gray-700 peer-checked:bg-[var(--status-bg)] peer-checked:text-[var(--status-text)] peer-checked:border-[var(--status-bg)] active:scale-[0.98]">
                                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 bg-[var(--status-bg)]"></span>
                                            {{ $option }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none resize-none"
                                      placeholder="Enter project description">{{ old('description') }}</textarea>
                        </div>

                        <div class="flex items-center gap-4 pt-6 border-t border-gray-200">
                            <button type="submit"
                                    class="px-6 py-3 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors font-medium">
                                Create Project
                            </button>
                            <a href="{{ route('home') }}"
                               class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
