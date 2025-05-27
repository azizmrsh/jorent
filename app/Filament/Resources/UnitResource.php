<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;


class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Real Estate';
    protected static ?string $navigationLabel = 'Units';
    protected static ?string $label = 'Unit';
    protected static ?string $pluralLabel = 'Units';
    protected static ?string $slug = 'units';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Property Selection')
                    ->schema([
                        Forms\Components\Select::make('property_id')
                            ->label('Property')
                            ->relationship('property', 'name')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Unit information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label('Unit Name')
                            ->maxLength(255),
                       Forms\Components\TextInput::make('unit_number')
                            ->label('Unit Number')
                            //->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('unit_type')
                            ->options([
                                'apartment' => 'Apartment',
                                'studio' => 'Studio',
                                'office' => 'Office',
                                'shop' => 'Shop',
                                'warehouse' => 'Warehouse',
                                'villa' => 'Villa',
                                'house' => 'House',
                                'building' => 'Building',
                            ])
                            ->required()
                            ->label('Unit Type'),
                        Forms\Components\TextInput::make('area')
                            ->numeric()
                            ->required()
                            ->label('Area (sqm)'),
                        Forms\Components\TextInput::make('rental_price')
                            ->numeric()
                            ->required()
                            ->label('Rental Price'),
                     ])->columns(2),
                /////////////
                Forms\Components\Section::make('Unit Details')
                    ->schema([

                        Forms\Components\Repeater::make('unit_details')
                            ->label('Unit Details')
                            ->schema([
                                Forms\Components\Select::make('detail_name')
                                    ->required()
                                    ->label('Detail Name')
                                    ->options([
                                        'kitchen' => 'Number of Kitchens',
                                        'bedrooms' => 'Number of Bedrooms',
                                        'bathrooms' => 'Number of Bathrooms',
                                        'balconies' => 'Number of Balconies',
                                        'parking_spaces' => 'Number of Parking Spaces',
                                        'floor' => 'Floor Number',
                                    ]),
                                Forms\Components\TextInput::make('detail_value')
                                    ->required()
                                    ->label('Detail Value')
                                    ->numeric()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $detailName = $get('detail_name');
                                        if (in_array($detailName, ['bedrooms', 'bathrooms', 'balconies', 'parking_spaces', 'floor', 'kitchen'])) {    
                                            $set('detail_value', (int) $state);
                                        }
                                    }),
                            ])
                            ->columns(2)
                            ->createItemButtonLabel('Add Detail'),
                    ]),

                ////////////////////    
                Forms\Components\Section::make('Unit Features')
                    ->schema([
                        Forms\Components\Repeater::make('features')
                            ->label('Features')
                            ->schema([
                                Forms\Components\Select::make('feature_name')
                                    ->required()
                                    ->label('Feature Name')
                                    ->options([
                                        'furnished' => 'Furnished',

                                        'elevator' => 'Elevator',
                                        'swimming_pool' => 'Swimming Pool',
                                        'gym' => 'Gym',
                                        'camera_security' => 'Camera Security',
                                        'parking' => 'Parking',
                                        'playground' => 'Playground',
                                        'wifi' => 'WiFi',
                                        'garden' => 'Garden',
                                        'security' => 'Security',
                                        'SMART_HOME' => 'Smart Home',
                                        'fire_alarm' => 'Fire Alarm',
                                        'central_air_conditioning' => 'Central Air Conditioning',
                                        'heating' => 'Heating',
                                        'fireplace' => 'Fireplace',
                                       
                                    ]),
                                Forms\Components\Select::make('feature_value')
                                    ->required()
                                    ->label('Feature Value')
                                    ->options([
                                        'yes' => 'YES',
                                        'no' => 'NO',
                                    ]),
                            ])
                            ->columns(2)
                            ->createItemButtonLabel('Add Feature'),
                    ]),
                 ///////////////////
                Forms\Components\Section::make('Images')
                    ->schema([
                        Forms\Components\Repeater::make('images')
                            ->label('Images')
                            ->schema([
                                Forms\Components\FileUpload::make('image')
                                    ->label('Image')
                                    ->image()
                                    ->directory('uploads/units')
                                    ->maxSize(1024),
                            ])
                            ->columns(1)
                            ->createItemButtonLabel('Add Image'),
                    ]),

                Forms\Components\Section::make('Additional Details')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->maxLength(65535),
                        Forms\Components\Select::make('status')
                            ->options([
                                'available' => 'Available',
                                'rented' => 'Rented',
                                'under_maintenance' => 'Under Maintenance',
                                'unavailable' => 'Unavailable',
                                'reserved' => 'Reserved',
                                'not_confirmed' => 'Not Confirmed',
                            ])
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('اسم الوحدة')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit_number')
                    ->label('رقم الوحدة')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label('العقار')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit_type')
                    ->label('نوع الوحدة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'apartment' => 'primary',
                        'villa' => 'success',
                        'warehouse' => 'warning',
                        'house' => 'info',
                        'building' => 'secondary',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('area')
                    ->label('المساحة (م²)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('rental_price')
                    ->label('سعر الإيجار')
                    ->money('SAR')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('الحالة')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',
                        'rented' => 'warning',
                        'under_maintenance' => 'danger',
                        'unavailable' => 'gray',
                        'reserved' => 'info',
                        'not_confirmed' => 'secondary',
                        default => 'gray',
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('تاريخ آخر تحديث')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label('ملاحظات')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label('العقار')
                    ->relationship('property', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('unit_type')
                    ->label('نوع الوحدة')
                    ->options([
                        'apartment' => 'شقة',
                        'villa' => 'فيلا',
                        'warehouse' => 'مستودع',
                        'house' => 'منزل',
                        'building' => 'مبنى',
                    ]),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('الحالة')
                    ->options([
                        'available' => 'متاحة',
                        'rented' => 'مؤجرة',
                        'under_maintenance' => 'تحت الصيانة',
                        'unavailable' => 'غير متاحة',
                        'reserved' => 'محجوزة',
                        'not_confirmed' => 'غير مؤكدة',
                    ]),
                    
                Tables\Filters\Filter::make('area_range')
                    ->label('نطاق المساحة')
                    ->form([
                        Forms\Components\TextInput::make('area_from')
                            ->label('من (م²)')
                            ->numeric(),
                        Forms\Components\TextInput::make('area_to')
                            ->label('إلى (م²)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['area_from'], fn ($query, $area) => $query->where('area', '>=', $area))
                            ->when($data['area_to'], fn ($query, $area) => $query->where('area', '<=', $area));
                    }),
                    
                Tables\Filters\Filter::make('price_range')
                    ->label('نطاق السعر')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('من (ريال)')
                            ->numeric(),
                        Forms\Components\TextInput::make('price_to')
                            ->label('إلى (ريال)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['price_from'], fn ($query, $price) => $query->where('rental_price', '>=', $price))
                            ->when($data['price_to'], fn ($query, $price) => $query->where('rental_price', '<=', $price));
                    }),
                    
                Tables\Filters\Filter::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('تصدير البيانات')
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export')
                        ->label('تصدير المحدد'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }

    public static function getRelations(): array
    {
        return [
            // Define relations here
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}
