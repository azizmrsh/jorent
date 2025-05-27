@php
    $isCollapsible = $collapsible ?? true;
    $isCollapsed = $collapsed ?? false;
    $groupId = 'widget-group-' . uniqid();
@endphp

<div class="bg-white shadow rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    
    {{-- Header --}}
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($icon ?? false)
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-gray-600 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                @endif
                
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $title ?: 'Widget Group' }}
                </h3>
            </div>
        </div>
    </div>

    {{-- Content --}}
    <div class="p-6 space-y-6">
        @if(!empty($widgets) && is_array($widgets))
            @foreach($widgets as $widget)
                <div class="widget-item">
                    @if(is_string($widget))
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-900 dark:text-white">{{ $widget }}</p>
                        </div>
                    @else
                        <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-900 dark:text-white">Widget Content</p>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="p-8 text-center">
                <div class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v1M7 8h10" />
                    </svg>
                </div>
                <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                    Widget Group Ready
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    This collapsible widget group is working correctly.
                </p>
            </div>
        @endif
    </div>
    
</div>
