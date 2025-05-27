<div 
    x-data="{ 
        collapsed: @js($collapsed),
        toggle() {
            this.collapsed = !this.collapsed;
            // حفظ الحالة في Local Storage
            localStorage.setItem('widget_group_{{ Str::slug($title) }}', this.collapsed ? '1' : '0');
        },
        init() {
            // استرداد الحالة من Local Storage
            const saved = localStorage.getItem('widget_group_{{ Str::slug($title) }}');
            if (saved !== null) {
                this.collapsed = saved === '1';
            }
        }
    }"
    class="mb-6"
>
    <!-- عنوان القسم القابل للطي -->
    <div 
        @if($collapsible) 
            @click="toggle()" 
            class="cursor-pointer select-none" 
        @endif
        class="flex items-center justify-between p-4 bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-200 mb-4"
    >
        <div class="flex items-center space-x-3 rtl:space-x-reverse">
            <span class="text-2xl">{{ $title }}</span>
        </div>
        
        @if($collapsible)
            <div class="flex items-center space-x-2 rtl:space-x-reverse text-gray-500 dark:text-gray-400">
                <span class="text-sm font-medium">
                    <span x-show="!collapsed" x-transition>إخفاء</span>
                    <span x-show="collapsed" x-transition>إظهار</span>
                </span>
                <div class="relative">
                    <svg 
                        x-bind:class="{ 'rotate-180': !collapsed }"
                        class="w-5 h-5 transition-transform duration-300 ease-out" 
                        fill="none" 
                        stroke="currentColor" 
                        viewBox="0 0 24 24"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        @endif
    </div>

    <!-- محتوى القسم (الـ Widgets) -->
    <div 
        x-show="!collapsed"
        x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="opacity-0 transform -translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 transform -translate-y-2 scale-98"
        class="space-y-4"
    >        <!-- Container للـ widgets مع تحسين التخطيط -->
        <div class="widget-grid">
            @foreach($widgets as $widget)
                <div class="widget-item">
                    @if(is_string($widget))
                        @livewire($widget)
                    @elseif(is_object($widget))
                        @livewire($widget::class)
                    @else
                        {{ $widget }}
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>

<style>
/* تحسين تصميم الـ widgets داخل المجموعات */
.widget-grid {
    display: grid;
    gap: 1rem;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
}

@media (min-width: 768px) {
    .widget-grid {
        gap: 1.5rem;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    }
}

@media (min-width: 1024px) {
    .widget-grid {
        gap: 2rem;
    }
}

.widget-item {
    @apply transform transition-all duration-200 hover:scale-105;
}

.widget-item .fi-wi-stats-overview-stat {
    @apply bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-shadow duration-200;
}

.widget-item .fi-wi-chart {
    @apply bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm;
}

/* تحسين عرض الرسوم البيانية */
.widget-item [class*="chart"] {
    min-height: 300px;
}

/* ضمان عرض الـ stats بشكل متناسق */
.widget-item [class*="stat"] {
    min-height: 120px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
</style>
