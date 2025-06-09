<x-filament-panels::page>
    {{-- Profile Statistics Header --}}
    <div class="mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-primary-600">{{ $this->getUserStatistics()['profile_completion'] }}%</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Profile Complete</div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-success-600">{{ $this->getUserStatistics()['security_score'] }}%</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Security Score</div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="text-center">
                    <div class="text-2xl font-bold text-info-600">{{ $this->getUserStatistics()['days_with_us'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('general.Days With Us') }}</div>
                </div>
            </x-filament::card>
            
            <x-filament::card>
                <div class="text-center">
                    <div class="text-lg font-bold text-gray-700 dark:text-gray-300">{{ ucfirst(auth()->user()->role ?? 'User') }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('general.Account Role') }}</div>
                </div>
            </x-filament::card>
        </div>
    </div>

    {{-- Main Profile Form --}}
    {{ $this->form }}
</x-filament-panels::page>
