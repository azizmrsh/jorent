<?php

namespace App\Filament\Widgets;

use App\Models\Unit;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UnitsTableWidget extends BaseWidget
{
    protected static ?int $sort = 53; // ترتيب منخفض لإظهاره في أسفل الصفحة
    protected int|string|array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Unit::query()
                    ->with(['property'])
                    ->withCount(['contracts' => function ($query) {
                        $query->where('status', 'active');
                    }])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الوحدة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit_number')
                    ->label('رقم الوحدة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('property.name')
                    ->label('العقار التابعة له')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('contracts_count')
                    ->label('العقود النشطة')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => 
                        $state > 0 ? 
                        "<span class='text-primary-600 font-medium'>{$state}</span>" : 
                        "<span class='text-success-600 font-medium'>متاح</span>"
                    )
                    ->html(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->heading('الوحدات')
            ->description('آخر 10 وحدات سكنية مضافة في النظام');
    }
}
