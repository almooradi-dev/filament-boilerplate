<x-filament-panels::page>

    {{-- ──────────────────────────────────────────────────────────
         Section 1 – Package Updates
    ─────────────────────────────────────────────────────────────── --}}
    <x-filament::section>
        <x-slot name="heading">Package Updates</x-slot>
        <x-slot name="description">Installed vs latest stable version for tracked packages.</x-slot>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @foreach ($this->getPackageStatuses() as $pkg)
                @php
                    $badgeMap = [
                        'up_to_date' => [
                            'color' => 'success',
                            'label' => 'Up to date',
                            'icon' => 'heroicon-s-check-circle',
                        ],
                        'patch' => [
                            'color' => 'info',
                            'label' => 'Patch update',
                            'icon' => 'heroicon-s-arrow-up-circle',
                        ],
                        'minor' => [
                            'color' => 'warning',
                            'label' => 'Minor update',
                            'icon' => 'heroicon-s-arrow-up-circle',
                        ],
                        'major' => [
                            'color' => 'danger',
                            'label' => 'Major update',
                            'icon' => 'heroicon-s-exclamation-circle',
                        ],
                        'unknown' => [
                            'color' => 'gray',
                            'label' => 'Unknown',
                            'icon' => 'heroicon-s-question-mark-circle',
                        ],
                    ];
                    $badge = $badgeMap[$pkg['update']] ?? $badgeMap['unknown'];
                @endphp

                <div
                    class="relative flex items-start gap-4 rounded-xl border border-gray-200 bg-white p-4 shadow-sm
                            dark:border-white/10 dark:bg-gray-900">

                    {{-- Package icon --}}
                    <div
                        class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg
                                bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        <x-filament::icon :icon="$pkg['icon']" class="h-6 w-6" />
                    </div>

                    {{-- Info --}}
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $pkg['label'] }}
                            </p>
                            <x-filament::badge :color="$badge['color']" :icon="$badge['icon']">
                                {{ $badge['label'] }}
                            </x-filament::badge>
                        </div>

                        <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                            {{ $pkg['name'] }}
                        </p>

                        <div class="mt-2 flex items-center gap-4 text-xs">
                            <span class="text-gray-600 dark:text-gray-300">
                                Installed:
                                <strong class="font-mono">{{ $pkg['current'] }}</strong>
                            </span>
                            <span class="text-gray-400">→</span>
                            <span class="text-gray-600 dark:text-gray-300">
                                Latest:
                                <strong
                                    class="font-mono {{ $pkg['update'] !== 'up_to_date' && $pkg['update'] !== 'unknown' ? 'text-primary-600 dark:text-primary-400' : '' }}">
                                    {{ $pkg['latest'] }}
                                </strong>
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-filament::section>


    {{-- ──────────────────────────────────────────────────────────
         Section 2 – Server Usage
    ─────────────────────────────────────────────────────────────── --}}
    @php $metrics = $this->getServerMetrics(); @endphp

    <x-filament::section class="mt-6">
        <x-slot name="heading">Server Usage</x-slot>
        <x-slot name="description">Real-time resource utilisation for the host running this application.</x-slot>

        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- ── CPU ── --}}
            @php
                $cpuPct = $metrics['cpu'];
                $cpuColor = $cpuPct >= 90 ? 'danger' : ($cpuPct >= 70 ? 'warning' : 'success');
            @endphp
            <x-filament::section compact>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-cpu-chip" class="h-5 w-5 text-gray-400" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">CPU</span>
                    </div>
                    <x-filament::badge :color="$cpuColor">{{ $cpuPct }}%</x-filament::badge>
                </div>

                <div class="mt-3">
                    <div class="mb-1 flex justify-between text-xs text-gray-500">
                        <span>Usage</span>
                        <span>{{ $cpuPct }}%</span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-full rounded-full transition-all
                                   {{ $cpuPct >= 90 ? 'bg-danger-500' : ($cpuPct >= 70 ? 'bg-warning-500' : 'bg-success-500') }}"
                            style="width: {{ min($cpuPct, 100) }}%"></div>
                    </div>
                </div>

                @php $load = $metrics['load']; @endphp
                <p class="mt-2 text-xs text-gray-400">
                    Load avg: {{ $load['1m'] }} / {{ $load['5m'] }} / {{ $load['15m'] }}
                    <span class="ml-1 text-gray-300 dark:text-gray-600">(1m / 5m / 15m)</span>
                </p>
            </x-filament::section>


            {{-- ── Memory ── --}}
            @php
                $mem = $metrics['memory'];
                $memColor = $mem['percent'] >= 90 ? 'danger' : ($mem['percent'] >= 75 ? 'warning' : 'success');
            @endphp
            <x-filament::section compact>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-circle-stack" class="h-5 w-5 text-gray-400" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Memory</span>
                    </div>
                    <x-filament::badge :color="$memColor">{{ $mem['percent'] }}%</x-filament::badge>
                </div>

                <div class="mt-3">
                    <div class="mb-1 flex justify-between text-xs text-gray-500">
                        <span>{{ number_format($mem['used'], 0) }} MB used</span>
                        <span>{{ number_format($mem['total'], 0) }} MB total</span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-full rounded-full transition-all
                                   {{ $mem['percent'] >= 90 ? 'bg-danger-500' : ($mem['percent'] >= 75 ? 'bg-warning-500' : 'bg-success-500') }}"
                            style="width: {{ min($mem['percent'], 100) }}%"></div>
                    </div>
                </div>

                <p class="mt-2 text-xs text-gray-400">
                    Free: {{ number_format($mem['free'], 0) }} MB
                </p>
            </x-filament::section>


            {{-- ── Disk ── --}}
            @php
                $disk = $metrics['disk'];
                $diskColor = $disk['percent'] >= 90 ? 'danger' : ($disk['percent'] >= 75 ? 'warning' : 'success');
            @endphp
            <x-filament::section compact>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-server-stack" class="h-5 w-5 text-gray-400" />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Disk</span>
                    </div>
                    <x-filament::badge :color="$diskColor">{{ $disk['percent'] }}%</x-filament::badge>
                </div>

                <div class="mt-3">
                    <div class="mb-1 flex justify-between text-xs text-gray-500">
                        <span>{{ $disk['used'] }} GB used</span>
                        <span>{{ $disk['total'] }} GB total</span>
                    </div>
                    <div class="h-2.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                        <div class="h-full rounded-full transition-all
                                   {{ $disk['percent'] >= 90 ? 'bg-danger-500' : ($disk['percent'] >= 75 ? 'bg-warning-500' : 'bg-success-500') }}"
                            style="width: {{ min($disk['percent'], 100) }}%"></div>
                    </div>
                </div>

                <p class="mt-2 text-xs text-gray-400">
                    Free: {{ $disk['free'] }} GB
                </p>
            </x-filament::section>


            {{-- ── Load Average (summary card) ── --}}
            <x-filament::section compact>
                <div class="flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-chart-bar" class="h-5 w-5 text-gray-400" />
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Load Average</span>
                </div>

                <div class="mt-4 grid grid-cols-3 divide-x divide-gray-200 text-center dark:divide-white/10">
                    @foreach (['1m' => '1 min', '5m' => '5 min', '15m' => '15 min'] as $key => $label)
                        <div class="px-2">
                            <p class="text-lg font-bold tabular-nums text-gray-900 dark:text-white">
                                {{ $metrics['load'][$key] }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $label }}</p>
                        </div>
                    @endforeach
                </div>

                <p class="mt-3 text-xs text-gray-400">
                    Higher than the CPU count may indicate congestion.
                </p>
            </x-filament::section>

        </div>
    </x-filament::section>

</x-filament-panels::page>
