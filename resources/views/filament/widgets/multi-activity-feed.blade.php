<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            🌟 Recent System Activities
        </x-slot>

        <div class="space-y-4">
            @forelse($activities as $activity)
                <div class="flex items-center space-x-4 p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
                    <!-- Activity Icon -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                            @if($activity['color'] === 'success') bg-green-100 text-green-600 dark:bg-green-900/20 dark:text-green-400
                            @elseif($activity['color'] === 'info') bg-blue-100 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400
                            @elseif($activity['color'] === 'warning') bg-yellow-100 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400
                            @elseif($activity['color'] === 'primary') bg-indigo-100 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400
                            @else bg-gray-100 text-gray-600 dark:bg-gray-900/20 dark:text-gray-400
                            @endif">
                            <x-heroicon-m-document-plus class="w-5 h-5" />
                        </div>
                    </div>

                    <!-- Activity Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <!-- Activity Type Badge -->
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mb-1
                                    @if($activity['color'] === 'success') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                    @elseif($activity['color'] === 'info') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300
                                    @elseif($activity['color'] === 'warning') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                    @elseif($activity['color'] === 'primary') bg-indigo-100 text-indigo-800 dark:bg-indigo-900/20 dark:text-indigo-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300
                                    @endif">
                                    {{ $activity['title'] }}
                                </span>

                                <!-- Activity Description -->
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $activity['description'] }}
                                </p>
                                
                                <!-- Activity Subtitle -->
                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                    {{ $activity['subtitle'] }}
                                </p>
                            </div>

                            <!-- Amount (if applicable) -->
                            @if($activity['amount'])
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ number_format($activity['amount'], 0) }} JOD
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Activity Date and Status -->
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $activity['date']->diffForHumans() }}
                            </span>
                            
                            <!-- Status Badge -->
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                @if($activity['status'] === 'active' || $activity['status'] === 'completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                @elseif($activity['status'] === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                @elseif($activity['status'] === 'inactive' || $activity['status'] === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300
                                @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300
                                @endif">
                                {{ ucfirst($activity['status']) }}
                            </span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8">
                    <x-heroicon-o-inbox class="mx-auto h-12 w-12 text-gray-400" />
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No recent activities</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        There are no recent activities in the system.
                    </p>
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
