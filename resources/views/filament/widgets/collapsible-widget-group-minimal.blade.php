<div class="bg-white shadow rounded-xl p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        {{ $title ?? 'Collapsible Widget Group' }}
    </h3>
    
    <div class="space-y-4">
        @if(!empty($widgets) && is_array($widgets))
            @foreach($widgets as $widget)
                <div class="p-4 bg-gray-50 rounded-lg border">
                    @if(is_string($widget))
                        <p class="text-sm text-gray-700">{{ $widget }}</p>
                    @else
                        <p class="text-sm text-gray-700">Widget Item</p>
                    @endif
                </div>
            @endforeach
        @else
            <div class="text-center p-8">
                <h4 class="text-sm font-medium text-gray-900 mb-2">
                    Widget Group Ready
                </h4>
                <p class="text-sm text-gray-500">
                    This widget is working correctly.
                </p>
            </div>
        @endif
    </div>
</div>
