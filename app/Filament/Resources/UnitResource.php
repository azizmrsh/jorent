<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Filament\Resources\UnitResource\RelationManagers;
use App\Models\Unit;
use App\Traits\FileUploadTrait;
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
    use FileUploadTrait;
    
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Rental Management';
    protected static ?string $navigationLabel = 'Units';
    protected static ?string $label = 'Unit';
    protected static ?string $pluralLabel = 'Units';
    protected static ?string $slug = 'units';
    protected static ?int $navigationSort = 2;

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
                        self::unitImagesUpload(),
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
                    ->label('Unit Name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit_number')
                    ->label('Unit Number')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label('Property')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit_type')
                    ->label('Unit Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'apartment' => 'primary',
                        'villa' => 'success',
                        'warehouse' => 'warning',
                        'house' => 'info',
                        'building' => 'secondary',
                        'studio' => 'purple',
                        'office' => 'orange',
                        'shop' => 'pink',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'apartment' => 'Apartment',
                        'villa' => 'Villa',
                        'warehouse' => 'Warehouse',
                        'house' => 'House',
                        'building' => 'Building',
                        'studio' => 'Studio',
                        'office' => 'Office',
                        'shop' => 'Shop',
                        default => ucfirst($state),
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('area')
                    ->label('Area (m²)')
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('rental_price')
                    ->label('Rental Price')
                    ->money('JOD')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
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
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'under_maintenance' => 'Under Maintenance',
                        'unavailable' => 'Unavailable',
                        'reserved' => 'Reserved',
                        'not_confirmed' => 'Not Confirmed',
                        default => ucfirst($state),
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('unit_type')
                    ->label('Unit Type')
                    ->options([
                        'apartment' => 'Apartment',
                        'villa' => 'Villa',
                        'warehouse' => 'Warehouse',
                        'house' => 'House',
                        'building' => 'Building',
                        'studio' => 'Studio',
                        'office' => 'Office',
                        'shop' => 'Shop',
                    ]),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'available' => 'Available',
                        'rented' => 'Rented',
                        'under_maintenance' => 'Under Maintenance',
                        'unavailable' => 'Unavailable',
                        'reserved' => 'Reserved',
                        'not_confirmed' => 'Not Confirmed',
                    ]),
                    
                Tables\Filters\Filter::make('area_range')
                    ->label('Area Range')
                    ->form([
                        Forms\Components\TextInput::make('area_from')
                            ->label('From (m²)')
                            ->numeric(),
                        Forms\Components\TextInput::make('area_to')
                            ->label('To (m²)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['area_from'], fn ($query, $area) => $query->where('area', '>=', $area))
                            ->when($data['area_to'], fn ($query, $area) => $query->where('area', '<=', $area));
                    }),
                    
                Tables\Filters\Filter::make('price_range')
                    ->label('Price Range')
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label('From (JOD)')
                            ->numeric(),
                        Forms\Components\TextInput::make('price_to')
                            ->label('To (JOD)')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['price_from'], fn ($query, $price) => $query->where('rental_price', '>=', $price))
                            ->when($data['price_to'], fn ($query, $price) => $query->where('rental_price', '<=', $price));
                    }),
                    
                Tables\Filters\Filter::make('created_at')
                    ->label('Creation Date')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('To Date'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export Data')
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
                        ->label('Export Selected'),
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
