<?php

namespace App\Filament\Resources\PropertyResource\Widgets;

use App\Models\Property;
use App\Models\Unit;
use App\Models\Contract1;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class PropertiesDetailedStats extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    
    protected function getStats(): array
    {
        
        // إحصائيات العقارات الأساسية
        $totalProperties = Property::count();
        $newPropertiesThisMonth = Property::where('created_at', '>=', Carbon::now()->startOfMonth())->count();
        $lastMonthProperties = Property::where('created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
                                      ->where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $propertiesGrowth = $lastMonthProperties > 0 ? 
            round((($newPropertiesThisMonth - $lastMonthProperties) / $lastMonthProperties) * 100, 1) : 0;

        // إحصائيات الوحدات
        $totalUnits = Unit::count();
        $availableUnits = Unit::whereDoesntHave('contracts', function ($query) {
            $query->where('status', 'active');
        })->count();
        $occupiedUnits = Unit::whereHas('contracts', function ($query) {
            $query->where('status', 'active');
        })->count();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;

        // أنواع العقارات
        $apartmentCount = Property::where('type', 'apartment')->count();
        $villaCount = Property::where('type', 'villa')->count();
        $commercialCount = Property::where('type', 'commercial')->count();
        $otherCount = $totalProperties - ($apartmentCount + $villaCount + $commercialCount);

        // متوسط الوحدات لكل عقار
        $avgUnitsPerProperty = $totalProperties > 0 ? round($totalUnits / $totalProperties, 1) : 0;

        // القيمة الإجمالية للإيجار
        $totalRentValue = Contract1::where('status', 'active')->sum('rent_amount');

        return [
            // الصف الأول - الإحصائيات الأساسية
            Stat::make('🏢 Total Properties', number_format($totalProperties))
                ->description($newPropertiesThisMonth . ' added this month')
                ->descriptionIcon($propertiesGrowth > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($propertiesGrowth > 0 ? 'success' : ($propertiesGrowth < 0 ? 'danger' : 'gray'))
                ->chart([7, 3, 4, 5, 6, 3, 5, 3, 6, 8, 10, $newPropertiesThisMonth])
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'
                ]),

            Stat::make('🏠 Total Units', number_format($totalUnits))
                ->description('Average: ' . $avgUnitsPerProperty . ' units per property')
                ->descriptionIcon('heroicon-m-home')
                ->color('info')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;'
                ]),

            Stat::make('📊 Occupancy Rate', $occupancyRate . '%')
                ->description($occupiedUnits . ' occupied, ' . $availableUnits . ' available')
                ->descriptionIcon($occupancyRate > 80 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($occupancyRate > 80 ? 'success' : ($occupancyRate < 50 ? 'danger' : 'warning'))
                ->chart([65, 70, 75, 78, 82, 85, 80, 75, 78, 82, 85, $occupancyRate])
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;'
                ]),

            Stat::make('💰 Total Rent Value', '$' . number_format($totalRentValue))
                ->description('Monthly rental income')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: white;'
                ]),

            // الصف الثاني - أنواع العقارات
            Stat::make('🏬 Apartments', number_format($apartmentCount))
                ->description(($totalProperties > 0 ? round(($apartmentCount / $totalProperties) * 100, 1) : 0) . '% of total')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: white;'
                ]),

            Stat::make('🏡 Villas', number_format($villaCount))
                ->description(($totalProperties > 0 ? round(($villaCount / $totalProperties) * 100, 1) : 0) . '% of total')
                ->descriptionIcon('heroicon-m-home-modern')
                ->color('success')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); color: #374151;'
                ]),

            Stat::make('🏪 Commercial', number_format($commercialCount))
                ->description(($totalProperties > 0 ? round(($commercialCount / $totalProperties) * 100, 1) : 0) . '% of total')
                ->descriptionIcon('heroicon-m-building-storefront')
                ->color('warning')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #374151;'
                ]),

            Stat::make('📋 Other Types', number_format($otherCount))
                ->description(($totalProperties > 0 ? round(($otherCount / $totalProperties) * 100, 1) : 0) . '% of total')
                ->descriptionIcon('heroicon-m-squares-plus')
                ->color('gray')
                ->extraAttributes([
                    'style' => 'background: linear-gradient(135deg, #d299c2 0%, #fef9d7 100%); color: #374151;'
                ]),
        ];
    }
}
