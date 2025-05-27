@php
    $isCollapsible = $collapsible ?? true;
    $isCollapsed = $collapsed ?? false;
    $groupId = 'widget-group-' . uniqid();
@endphp

<div class="fi-wi-collapsible-group bg-white shadow rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
     x-data="{ 
        collapsed: @js($isCollapsed),
        toggle() {
            this.collapsed = !this.collapsed;
        }
     }">
    
    {{-- Header --}}
    <div class="fi-wi-collapsible-header p-6 border-b border-gray-200 dark:border-gray-700"
         @if($isCollapsible)
         role="button"
         aria-expanded="false"
         :aria-expanded="!collapsed"
         aria-controls="{{ $groupId }}"
         @click="toggle()"
         class="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
         @endif>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="flex-shrink-0">
                        @svg($icon, 'h-5 w-5 text-gray-600 dark:text-gray-400')
                    </div>
                @endif
                
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $title }}
                </h3>
            </div>
            
            @if($isCollapsible)
                <button 
                    type="button"
                    class="flex-shrink-0 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    :aria-label="collapsed ? 'Expand section' : 'Collapse section'"
                    @click.stop="toggle()">
                    
                    <svg class="h-5 w-5 text-gray-500 dark:text-gray-400 transition-transform duration-200"
                         :class="{ 'rotate-180': !collapsed }"
                         fill="none" 
                         viewBox="0 0 24 24" 
                         stroke="currentColor">
                        <path stroke-linecap="round" 
                              stroke-linejoin="round" 
                              stroke-width="2" 
                              d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            @endif
        </div>
    </div>

    {{-- Content --}}
    <div id="{{ $groupId }}"
         class="fi-wi-collapsible-content"
         x-show="!collapsed"
         x-collapse.duration.300ms
         role="region"
         :aria-hidden="collapsed">
        
        <div class="p-6 space-y-6">
            @if(!empty($widgets))
                @foreach($widgets as $widget)
                    <div class="fi-wi-item">
                        @if(is_string($widget))
                            {{-- If widget is a class name, try to instantiate it --}}
                            @if(class_exists($widget))
                                @php
                                    $widgetInstance = app($widget);
                                @endphp
                                @if(method_exists($widgetInstance, 'render'))
                                    {!! $widgetInstance->render() !!}
                                @else
                                    <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Widget: {{ $widget }}
                                        </p>
                                    </div>
                                @endif
                            @else
                                {{-- Treat as plain content --}}
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $widget }}
                                    </p>
                                </div>
                            @endif
                        @elseif(is_object($widget))
                            {{-- If widget is an object, try to render it --}}
                            @if(method_exists($widget, 'render'))
                                {!! $widget->render() !!}
                            @elseif(method_exists($widget, '__toString'))
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {!! $widget !!}
                                    </p>
                                </div>
                            @else
                                <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Object: {{ get_class($widget) }}
                                    </p>
                                </div>
                            @endif
                        @elseif(is_array($widget))
                            {{-- Handle array of widget data --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                @if(isset($widget['content']))
                                    {!! $widget['content'] !!}
                                @elseif(isset($widget['title']) || isset($widget['description']))
                                    @if(isset($widget['title']))
                                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">
                                            {{ $widget['title'] }}
                                        </h4>
                                    @endif
                                    @if(isset($widget['description']))
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $widget['description'] }}
                                        </p>
                                    @endif
                                @else
                                    <pre class="text-xs text-gray-600 dark:text-gray-400 overflow-x-auto">{{ json_encode($widget, JSON_PRETTY_PRINT) }}</pre>
                                @endif
                            </div>
                        @else
                            {{-- Handle other types (strings, numbers, etc.) --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $widget }}
                                </p>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="p-8 text-center">
                    <div class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500 mb-4">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" 
                                  stroke-linejoin="round" 
                                  stroke-width="2" 
                                  d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v1M7 8h10" />
                        </svg>
                    </div>
                    <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-2">
                        No widgets configured
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Add widgets to this group to see them here.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Additional styles for the collapsible animation if not already included --}}
@pushOnce('styles')
<style>
    [x-cloak] { 
        display: none !important; 
    }
    
    .fi-wi-collapsible-header[role="button"]:hover {
        background-color: rgb(249 250 251 / 1);
    }
    
    .dark .fi-wi-collapsible-header[role="button"]:hover {
        background-color: rgb(31 41 55 / 1);
    }

    .fi-wi-collapsible-content {
        overflow: hidden;
    }
</style>
@endPushOnce
