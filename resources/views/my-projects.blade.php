<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Projects – {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased min-h-screen bg-slate-100">
    @include('components.header')

    <div class="flex h-[calc(100vh-64px)] overflow-hidden">
        @include('components.nav')

        <main class="flex-1 min-w-0 min-h-0 overflow-y-auto bg-gray-50">
            @if(session('success'))
                <div class="mx-6 md:mx-8 mt-6 p-4 bg-green-50 border border-green-200 rounded-lg text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            {{-- Gantt Charts for My Assigned Projects --}}
            <div class="w-full p-6 md:p-8 space-y-10">
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">My Project Timeline</h2>
                        <p class="text-sm text-gray-400 mt-1">Projects assigned to you — Gantt chart with status colours</p>
                    </div>
                    <div id="gantt-status-legend" class="flex flex-wrap gap-x-4 gap-y-2 sm:justify-end">
                        @foreach(\App\Models\Project::STATUS_COLORS as $status => $color)
                            <div class="flex items-center gap-1.5">
                                <div class="w-3 h-3 rounded-sm border border-gray-300 flex-shrink-0"
                                     style="background-color: {{ $color }}"></div>
                                <span class="text-xs text-gray-600 whitespace-nowrap">{{ $status }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @foreach($chartDataByAssignee ?? [] as $assignedTo => $chartData)
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-4 gap-3">
                        <h3 class="text-lg font-semibold text-gray-800">{{ $assignedTo }}</h3>
                        <button type="button"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-200 export-gantt-pdf"
                                data-target-id="gantt-chart-{{ Str::slug($assignedTo) }}"
                                data-title="{{ $assignedTo }}">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 16l-4-4m4 4l4-4m-4 4V4m-7 8v6a2 2 0 002 2h10a2 2 0 002-2v-6" />
                            </svg>
                            <span>Download PDF</span>
                        </button>
                    </div>
                    <div class="w-full overflow-x-auto gantt-export-wrap">
                        <div id="gantt-chart-{{ Str::slug($assignedTo) }}" class="min-h-[120px] gantt-chart-container" data-chart="{{ base64_encode(json_encode($chartData)) }}"></div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Project Cards --}}
            <div class="px-6 md:px-8 pb-10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-semibold text-gray-800">
                        My Projects
                        @if($projects->isNotEmpty())
                            <span class="ml-2 text-sm font-normal text-gray-400">({{ $projects->count() }})</span>
                        @endif
                    </h3>
                    <a href="{{ route('projects.add') }}"
                       class="flex items-center gap-1.5 text-sm text-orange-500 hover:text-orange-600 font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Add Project
                    </a>
                </div>

                @if($projects->isEmpty())
                    <div class="bg-white rounded-xl border-2 border-dashed border-gray-200 py-12 text-center shadow-sm">
                        <p class="text-gray-400 text-sm">No projects assigned to you yet.</p>
                        <p class="text-gray-300 text-xs mt-1">
                            Projects assigned to you will appear here.
                        </p>
                    </div>
                @else
                    @foreach($projectsGrouped ?? $projects->groupBy('assigned_to') as $assignedTo => $groupedProjects)
                    <div class="mb-8">
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">{{ $assignedTo }}</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($groupedProjects as $project)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex flex-col gap-4 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-semibold text-gray-800 leading-snug">{{ $project->name }}</h4>
                                        @if($project->assigned_to === Auth::user()->name || $project->user_id === Auth::id())
                                        <a href="{{ route('projects.edit', $project) }}" class="text-xs text-orange-500 hover:text-orange-600 font-medium mt-1 inline-block">
                                            Edit project
                                        </a>
                                        @endif
                                    </div>
                                    <span class="flex-shrink-0 px-2.5 py-1 rounded-full text-xs font-semibold border border-gray-200 whitespace-nowrap"
                                          style="background-color: {{ \App\Models\Project::STATUS_COLORS[$project->status] ?? '#D3D3D3' }}; color: {{ $project->status === 'New' ? '#555' : ($project->status === 'On' ? '#065f46' : ($project->status === 'Pause' ? '#7c2d12' : ($project->status === 'Waiting For Approval' ? '#f5f3ff' : ($project->status === 'Ticked Task' ? '#eff6ff' : '#f0fdf4')))) }}">
                                        {{ $project->status }}
                                    </span>
                                </div>
                                <div class="space-y-1.5 text-sm text-gray-500">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        <span>{{ $project->assigned_to }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($project->start_date)->format('M j, Y') }} – {{ \Carbon\Carbon::parse($project->end_date)->format('M j, Y') }}</span>
                                    </div>
                                </div>
                                @if($project->description)
                                    <p class="text-sm text-gray-400 line-clamp-2">{{ $project->description }}</p>
                                @endif
                                <div class="h-1.5 rounded-full w-full"
                                     style="background-color: {{ \App\Models\Project::STATUS_COLORS[$project->status] ?? '#D3D3D3' }}"></div>
                                @if($project->assigned_to === Auth::user()->name || $project->user_id === Auth::id())
                                <div>
                                    <label class="block text-xs font-medium text-gray-400 mb-1.5 uppercase tracking-wide">Change Status</label>
                                    <form action="{{ route('projects.updateStatus', $project) }}" method="POST" class="status-form">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status"
                                                class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-white text-gray-700 focus:ring-2 focus:ring-orange-500 focus:border-transparent outline-none cursor-pointer status-select">
                                            @foreach(\App\Models\Project::STATUS_OPTIONS as $opt)
                                                <option value="{{ $opt }}" {{ $project->status === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </div>
                                @endif
                            </div>
                        @endforeach
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </main>
    </div>

    <script>
        document.querySelectorAll('[id^="gantt-chart-"]').forEach(function(el) {
            const raw = el.getAttribute('data-chart');
            if (!raw) return;
            const chartData = JSON.parse(atob(raw));
            if (!chartData || chartData.length === 0) return;

            const uniqueRows = [...new Set(chartData.map(d => d.x))].length;
            const isMobile = window.matchMedia('(max-width: 640px)').matches;
            const minDate = Math.min(...chartData.map(d => d.y[0]));
            const maxDate = Math.max(...chartData.map(d => d.y[1]));
            const rangeDays = (maxDate - minDate) / (24 * 60 * 60 * 1000);
            const containerWidth = el.parentElement ? el.parentElement.offsetWidth : 800;
            const chartWidth = Math.max(containerWidth, Math.ceil(rangeDays * 48));
            el.style.minWidth = chartWidth + 'px';
            const opts = {
                series: [{ name: 'Timeline', data: chartData }],
                chart: {
                    type: 'rangeBar',
                    width: chartWidth,
                    height: Math.max(120, uniqueRows * 60 + 60),
                    toolbar: { show: false },
                    fontFamily: 'inherit'
                },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        barHeight: '60%',
                        rangeBarGroupRows: false,
                        borderRadius: 4
                    }
                },
                colors: chartData.map(d => d.fillColor),
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        const d = opts.w.config.series[opts.seriesIndex].data[opts.dataPointIndex];
                        const diffMs = d.y[1] - d.y[0];
                        const totalMinutes = Math.max(1, Math.round(diffMs / (60 * 1000)));
                        const days = Math.floor(totalMinutes / (24 * 60));
                        const hours = Math.floor((totalMinutes % (24 * 60)) / 60);
                        const minutes = totalMinutes % 60;
                        const parts = [];
                        if (days > 0) parts.push(days + 'd');
                        if (hours > 0) parts.push(hours + 'h');
                        if (minutes > 0 && days === 0) parts.push(minutes + 'm');
                        return parts.join(' ');
                    },
                    offsetY: -6,
                    style: {
                        fontSize: '11px',
                        fontWeight: 600,
                        colors: ['#111827']
                    },
                    background: {
                        enabled: false
                    }
                },
                xaxis: {
                    type: 'datetime',
                    labels: { datetimeFormatter: { day: 'MMM d', month: 'MMM d', year: 'MMM yyyy' } }
                },
                yaxis: {
                    labels: {
                        style: { fontWeight: 600, colors: '#374151' },
                        offsetX: isMobile ? 0 : 8
                    }
                },
                grid: {
                    borderColor: '#f0f0f0',
                    strokeDashArray: 3,
                    xaxis: { lines: { show: true } },
                    yaxis: { lines: { show: false } },
                    padding: {
                        left: isMobile ? 8 : 24,
                        right: isMobile ? 8 : 24
                    }
                },
                tooltip: {
                    custom: function(_opts) {
                        const d = _opts.w.config.series[_opts.seriesIndex].data[_opts.dataPointIndex];
                        const start = new Date(d.y[0]).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        const end = new Date(d.y[1]).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                        const diffMs = d.y[1] - d.y[0];
                        const totalMinutes = Math.max(1, Math.round(diffMs / (60 * 1000)));
                        const days = Math.floor(totalMinutes / (24 * 60));
                        const hours = Math.floor((totalMinutes % (24 * 60)) / 60);
                        const minutes = totalMinutes % 60;
                        const parts = [];
                        if (days > 0) parts.push(days + 'd');
                        if (hours > 0) parts.push(hours + 'h');
                        if (minutes > 0) parts.push(minutes + 'm');
                        const durationLabel = parts.join(' ');

                        const isTicketSegment = d.status === 'Ticked Task' && (d.ticketFrom || d.ticketDescription);
                        let ticketHtml = '';
                        if (isTicketSegment) {
                            if (d.ticketFrom) {
                                ticketHtml += '<p class="text-xs text-gray-700 mt-2"><span class="font-semibold">Ticket from:</span> ' +
                                    d.ticketFrom + '</p>';
                            }
                            if (d.ticketDescription) {
                                ticketHtml += '<p class="text-xs text-gray-500 mt-1 line-clamp-3"><span class="font-semibold">Description:</span> ' +
                                    d.ticketDescription + '</p>';
                            }
                        }

                        return '<div class="bg-white p-4 border border-gray-200 rounded-lg shadow-xl max-w-xs">' +
                            '<p class="font-semibold mb-2 text-gray-900">' + d.x + '</p>' +
                            '<p class="text-sm text-gray-700">' + (d.status || '') + '</p>' +
                            '<p class="text-xs text-gray-500 mt-1">' + start + ' – ' + end + ' (' + durationLabel + ')</p>' +
                            ticketHtml +
                            '</div>';
                    }
                }
            };
            new ApexCharts(el, opts).render();
        });

        document.querySelectorAll('.status-select').forEach(sel => {
            sel.addEventListener('change', function() {
                this.closest('.status-form').submit();
            });
        });

        async function exportGanttToPdf(targetId, titleText) {
            const chartEl = document.getElementById(targetId);
            if (!chartEl || !window.jspdf || !window.html2canvas) return;

            const wrap = chartEl.closest('.gantt-export-wrap') || chartEl.parentElement || chartEl;
            const fullWidth = Math.max(chartEl.scrollWidth || 0, chartEl.offsetWidth || 0, wrap.scrollWidth || 0, wrap.offsetWidth || 0);
            const fullHeight = Math.max(wrap.scrollHeight || 0, wrap.offsetHeight || 0);

            const tempHost = document.createElement('div');
            tempHost.style.position = 'fixed';
            tempHost.style.left = '-100000px';
            tempHost.style.top = '0';
            tempHost.style.width = fullWidth + 'px';
            tempHost.style.background = '#ffffff';

            const clone = wrap.cloneNode(true);
            clone.style.overflow = 'visible';
            clone.style.width = fullWidth + 'px';

            const exportHeader = document.createElement('div');
            exportHeader.style.padding = '12px 12px 6px 12px';
            exportHeader.style.display = 'flex';
            exportHeader.style.flexDirection = 'column';
            exportHeader.style.gap = '6px';

            const title = document.createElement('div');
            title.style.fontSize = '14px';
            title.style.fontWeight = '700';
            title.style.color = '#111827';
            title.textContent = (titleText ? titleText + ' — ' : '') + 'Project Timeline';
            exportHeader.appendChild(title);

            const legend = document.getElementById('gantt-status-legend');
            if (legend) {
                const legendClone = legend.cloneNode(true);
                legendClone.removeAttribute('id');
                legendClone.className = '';
                legendClone.style.display = 'flex';
                legendClone.style.flexWrap = 'wrap';
                legendClone.style.gap = '6px 14px';
                legendClone.style.alignItems = 'center';
                legendClone.style.fontSize = '10px';
                exportHeader.appendChild(legendClone);
            }

            const exportBody = document.createElement('div');
            exportBody.style.padding = '0 12px 12px 12px';
            exportBody.appendChild(clone);

            tempHost.appendChild(exportHeader);
            tempHost.appendChild(exportBody);

            document.body.appendChild(tempHost);

            await new Promise(r => requestAnimationFrame(() => r()));

            const canvas = await html2canvas(tempHost, {
                scale: 2,
                useCORS: true,
                backgroundColor: '#ffffff',
                width: fullWidth,
                windowWidth: fullWidth,
                scrollX: 0,
                scrollY: 0
            });

            document.body.removeChild(tempHost);

            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({ orientation: 'landscape', unit: 'pt', format: 'a4' });
            const pageWidth = pdf.internal.pageSize.getWidth();
            const pageHeight = pdf.internal.pageSize.getHeight();
            const ratio = Math.min(pageWidth / canvas.width, pageHeight / canvas.height);
            const imgWidth = canvas.width * ratio;
            const imgHeight = canvas.height * ratio;
            const x = (pageWidth - imgWidth) / 2;
            const y = (pageHeight - imgHeight) / 2;

            pdf.addImage(imgData, 'PNG', x, y, imgWidth, imgHeight);
            pdf.save(`project-timeline-${targetId}.pdf`);
        }

        document.querySelectorAll('.export-gantt-pdf').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target-id');
                const titleText = this.getAttribute('data-title') || '';
                exportGanttToPdf(targetId, titleText);
            });
        });
    </script>
</body>
</html>
