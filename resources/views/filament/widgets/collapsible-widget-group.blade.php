@php
    $isCollapsible = $collapsible ?? true;
    $isCollapsed = $collapsed ?? false;
    $groupId = 'widget-group-' . uniqid();
@endphp

<div class="bg-white shadow rounded-xl ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
     x-data="{ 
        collapsed: @js($isCollapsed),
        toggle() {
            this.collapsed = !this.collapsed;
        }
     }">
      {{-- Header --}}
    <div class="p-6 border-b border-gray-200 dark:border-gray-700 
               @if($isCollapsible) cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors @endif"
         @if($isCollapsible)
         role="button"
         aria-expanded="false"
         :aria-expanded="!collapsed"
         aria-controls="{{ $groupId }}"
         @click="toggle()"
         @endif>
        
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                @if($icon)
                    <div class="flex-shrink-0">
                        <x-heroicon-o-cube class="h-5 w-5 text-gray-600 dark:text-gray-400" />
                    </div>
                @endif
                
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    {{ $title ?: 'Widget Group' }}
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
    </div>    {{-- Content --}}
    <div id="{{ $groupId }}"
         class="overflow-hidden"
         x-show="!collapsed"
         x-collapse.duration.300ms
         role="region"
         :aria-hidden="collapsed">
        
        <div class="p-6 space-y-6">
            @if(!empty($widgets) && is_array($widgets))
                @foreach($widgets as $widget)
                    <div class="widget-item">
                        @if(is_string($widget) && class_exists($widget))
                            {{-- Render widget class --}}
                            @php
                                try {
                                    $widgetInstance = app($widget);
                                    if (method_exists($widgetInstance, 'render')) {
                                        echo $widgetInstance->render();
                                    } else {
                                        echo '<div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg"><p class="text-sm text-gray-600 dark:text-gray-400">Widget: ' . $widget . '</p></div>';
                                    }
                                } catch (Exception $e) {
                                    echo '<div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800"><p class="text-sm text-red-600 dark:text-red-400">Error loading widget: ' . $widget . '</p></div>';
                                }
                            @endphp
                        @elseif(is_object($widget) && method_exists($widget, 'render'))
                            {{-- Render widget object --}}
                            {!! $widget->render() !!}
                        @else
                            {{-- Fallback content --}}
                            <div class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                <p class="text-sm text-gray-900 dark:text-white">
                                    @if(is_string($widget))
                                        {{ $widget }}
                                    @elseif(is_array($widget))
                                        @if(isset($widget['title']))
                                            <strong>{{ $widget['title'] }}</strong>
                                        @endif
                                        @if(isset($widget['content']))
                                            <br>{{ $widget['content'] }}
                                        @endif
                                    @else
                                        Content: {{ json_encode($widget) }}
                                    @endif
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
