<?php

namespace App\Filament\Widgets;

use App\Models\Property;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PropertiesTableWidget extends BaseWidget
{
    protected static ?int $sort = 51; // ترتيب منخفض لإظهاره في أسفل الصفحة
    protected int|string|array $columnSpan = 'full';
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Property::query()
                    ->withCount(['units', 'contracts' => function ($query) {
                        $query->where('status', 'active');
                    }])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم العقار')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type1')
                    ->label('النوع')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type2')
                    ->label('الفئة')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('الوصف')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\TextColumn::make('units_count')
                    ->label('عدد الوحدات')
                    ->sortable(),
                Tables\Columns\TextColumn::make('contracts_count')
                    ->label('العقود النشطة')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->heading('العقارات')
            ->description('آخر 10 عقارات مضافة إلى النظام');
    }
}
