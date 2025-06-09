<?php

namespace App\Filament\Tenant\Resources;

use App\Models\Contract1;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tenant\Resources\MyContractResource\Pages;

class MyContractResource extends Resource
{
    protected static ?string $model = Contract1::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $navigationLabel = 'عقودي';
    
    protected static ?string $modelLabel = 'عقد';
    
    protected static ?string $pluralModelLabel = 'العقود';
    
    protected static ?string $navigationGroup = 'عقودي';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tenant_id', auth('tenant')->id())
            ->with(['unit.property', 'payments']);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('معلومات العقد')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('contract_number')
                                    ->label('رقم العقد'),
                                Infolists\Components\TextEntry::make('status')
                                    ->label('حالة العقد')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'active' => 'success',
                                        'pending' => 'warning',
                                        'terminated' => 'danger',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn (string $state): string => match ($state) {
                                        'active' => 'نشط',
                                        'pending' => 'معلق',
                                        'terminated' => 'منتهي',
                                        default => $state,
                                    }),
                            ]),
                    ]),
                    
                Infolists\Components\Section::make('معلومات العقار والوحدة')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('unit.property.name')
                                    ->label('اسم العقار'),
                                Infolists\Components\TextEntry::make('unit.unit_number')
                                    ->label('رقم الوحدة'),
                                Infolists\Components\TextEntry::make('unit.property.address')
                                    ->label('عنوان العقار')
                                    ->columnSpanFull(),
                            ]),
                    ]),
                    
                Infolists\Components\Section::make('التفاصيل المالية')
                    ->schema([
                        Infolists\Components\Grid::make(3)
                            ->schema([
                                Infolists\Components\TextEntry::make('monthly_rent')
                                    ->label('الإيجار الشهري')
                                    ->money('SAR'),
                                Infolists\Components\TextEntry::make('security_deposit')
                                    ->label('التأمين')
                                    ->money('SAR'),
                                Infolists\Components\TextEntry::make('commission')
                                    ->label('العمولة')
                                    ->money('SAR'),
                            ]),
                    ]),
                    
                Infolists\Components\Section::make('مواعيد العقد')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('start_date')
                                    ->label('تاريخ البداية')
                                    ->date('Y-m-d'),
                                Infolists\Components\TextEntry::make('end_date')
                                    ->label('تاريخ الانتهاء')
                                    ->date('Y-m-d'),
                            ]),
                    ]),
                    
                Infolists\Components\Section::make('ملاحظات')
                    ->schema([
                        Infolists\Components\TextEntry::make('notes')
                            ->label('الملاحظات')
                            ->columnSpanFull()
                            ->placeholder('لا توجد ملاحظات'),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contract_number')
                    ->label('رقم العقد')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('unit.property.name')
                    ->label('اسم العقار')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('unit.unit_number')
                    ->label('رقم الوحدة')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label('تاريخ البداية')
                    ->date('Y-m-d')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label('تاريخ الانتهاء')
                    ->date('Y-m-d')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('monthly_rent')
                    ->label('الإيجار الشهري')
                    ->money('SAR')
                    ->sortable(),
                    
                Tables\Columns\BadgeColumn::make('status')
                    ->label('الحالة')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'pending',
                        'danger' => 'terminated',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'نشط',
                        'pending' => 'معلق',
                        'terminated' => 'منتهي',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('حالة العقد')
                    ->options([
                        'active' => 'نشط',
                        'pending' => 'معلق',
                        'terminated' => 'منتهي',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('عرض التفاصيل'),
            ])
            ->bulkActions([
                // لا توجد إجراءات جماعية للمستأجرين
            ])
            ->emptyStateHeading('لا توجد عقود')
            ->emptyStateDescription('لم يتم العثور على أي عقود مرتبطة بحسابك')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMyContracts::route('/'),
            'view' => Pages\ViewMyContract::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // المستأجرون لا يمكنهم إنشاء عقود
    }

    public static function canEdit($record): bool
    {
        return false; // المستأجرون لا يمكنهم تعديل العقود
    }

    public static function canDelete($record): bool
    {
        return false; // المستأجرون لا يمكنهم حذف العقود
    }
}
