<div 
    x-data="{ 
        collapsed: true,
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('types_stats_collapsed', this.collapsed ? '1' : '0');
        },
        init() {
            const saved = localStorage.getItem('types_stats_collapsed');
            if (saved !== null) {
                this.collapsed = saved === '1';
            }
        }
    }"
    class="mb-6"
>
    <!-- عنوان القسم -->
    <div 
        @click="toggle()" 
        class="cursor-pointer select-none flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border border-green-200 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200 mb-4"
    >
        <div class="flex items-center space-x-3">
            <span class="text-2xl">🏘️</span>
            <span class="text-lg font-semibold text-gray-900 dark:text-white">تفصيل أنواع العقارات</span>
        </div>
        
        <div class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
            <span class="text-sm font-medium">
                <span x-show="!collapsed" x-transition>إخفاء</span>
                <span x-show="collapsed" x-transition>إظهار</span>
            </span>
            <svg 
                x-bind:class="{ 'rotate-180': !collapsed }"
                class="w-5 h-5 transition-transform duration-300" 
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>
    </div>

    <!-- محتوى القسم -->
    <div 
        x-show="!collapsed"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-2"
        class="widget-container"    >
        @livewire('app.filament.resources.property-resource.widgets.property-types-stats')
    </div>

    <style>
    .widget-container {
        @apply transform transition-all duration-200;
    }

    .widget-container .fi-wi-stats-overview {
        @apply bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm;
    }
    </style>
</div>
