<x-filament-panels::page>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

        @foreach ($this->getServiceStatuses() as $service)
            @php $running = $service['running']; @endphp

            <div
                class="rounded-2xl border bg-white dark:bg-gray-900 dark:border-gray-700 shadow-sm p-6 flex flex-col gap-4">

                {{-- Header --}}
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="p-2 rounded-xl {{ $running ? 'bg-success-100 text-success-600 dark:bg-success-900 dark:text-success-400' : 'bg-gray-100 text-gray-400 dark:bg-gray-800' }}">
                            <x-filament::icon :icon="$service['icon']" class="w-6 h-6" />
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 dark:text-white text-base">
                                {{ $service['label'] }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $service['description'] }}
                            </div>
                        </div>
                    </div>

                    {{-- Status Badge --}}
                    <span
                        class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-medium
                        {{ $running
                            ? 'bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300'
                            : 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400' }}">
                        <span
                            class="w-1.5 h-1.5 rounded-full {{ $running ? 'bg-success-500 animate-pulse' : 'bg-gray-400' }}"></span>
                        {{ $running ? 'Running' : 'Stopped' }}
                    </span>
                </div>

                {{-- Actions --}}
                <div class="flex gap-2 pt-2 border-t border-gray-100 dark:border-gray-800">

                    @if (!$running)
                        <button wire:click="startService('{{ $service['key'] }}')" style="background-color: #16a34a;"
                            onmouseover="this.style.backgroundColor='#15803d'"
                            onmouseout="this.style.backgroundColor='#16a34a'"
                            class="flex-1 inline-flex justify-center items-center gap-1.5 rounded-lg text-white text-sm font-medium px-4 py-2 transition">
                            <x-filament::icon icon="heroicon-o-play" class="w-4 h-4" />
                            Start
                        </button>
                    @else
                        <button wire:click="restartService('{{ $service['key'] }}')" style="background-color: #f59e0b;"
                            onmouseover="this.style.backgroundColor='#d97706'"
                            onmouseout="this.style.backgroundColor='#f59e0b'"
                            class="flex-1 inline-flex justify-center items-center gap-1.5 rounded-lg text-white text-sm font-medium px-4 py-2 transition">
                            <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4" />
                            Restart
                        </button>

                        <button wire:click="stopService('{{ $service['key'] }}')" style="background-color: #dc2626;"
                            onmouseover="this.style.backgroundColor='#b91c1c'"
                            onmouseout="this.style.backgroundColor='#dc2626'"
                            class="flex-1 inline-flex justify-center items-center gap-1.5 rounded-lg text-white text-sm font-medium px-4 py-2 transition">
                            <x-filament::icon icon="heroicon-o-stop" class="w-4 h-4" />
                            Stop
                        </button>
                    @endif

                </div>
            </div>
        @endforeach

    </div>

</x-filament-panels::page>
