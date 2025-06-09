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
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = 'Real Estate ';

    protected static ?string $label = null;
    protected static ?string $pluralLabel = null;
    protected static ?string $slug = 'units';

    public static function getNavigationLabel(): string
    {
        return __('general.Units');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Unit');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Units');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('general.Property Selection'))
                    ->schema([
                        Forms\Components\Select::make('property_id')
                            ->label(__('general.Property'))
                            ->relationship('property', 'name')
                            ->required(),
                    ]),

                Forms\Components\Section::make(__('general.Unit information'))
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->label(__('general.Unit Name'))
                            ->maxLength(255),
                       Forms\Components\TextInput::make('unit_number')
                            ->label(__('general.Unit Number'))
                            //->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('unit_type')
                            ->options([
                                'apartment' => __('general.Apartment'),
                                'studio' => __('general.Studio'),
                                'office' => __('general.Office'),
                                'shop' => __('general.Shop'),
                                'warehouse' => __('general.Warehouse'),
                                'villa' => __('general.Villa'),
                                'house' => __('general.House'),
                                'building' => __('general.Building'),
                            ])
                            ->required()
                            ->label(__('general.Unit Type')),
                        Forms\Components\TextInput::make('area')
                            ->numeric()
                            ->required()
                            ->label(__('general.Area (sqm)')),
                        Forms\Components\TextInput::make('rental_price')
                            ->numeric()
                            ->required()
                            ->label(__('general.Rental Price')),
                     ])->columns(2),
                /////////////
                Forms\Components\Section::make(__('general.Unit Details'))
                    ->schema([

                        Forms\Components\Repeater::make('unit_details')
                            ->label(__('general.Unit Details'))
                            ->schema([
                                Forms\Components\Select::make('detail_name')
                                    ->required()
                                    ->label(__('general.Detail Name'))
                                    ->options([
                                        'kitchen' => __('general.Number of Kitchens'),
                                        'bedrooms' => __('general.Number of Bedrooms'),
                                        'bathrooms' => __('general.Number of Bathrooms'),
                                        'balconies' => __('general.Number of Balconies'),
                                        'parking_spaces' => __('general.Number of Parking Spaces'),
                                        'floor' => __('general.Floor Number'),
                                    ]),
                                Forms\Components\TextInput::make('detail_value')
                                    ->required()
                                    ->label(__('general.Detail Value'))
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
                            ->createItemButtonLabel(__('general.Add Detail')),
                    ]),

                ////////////////////    
                Forms\Components\Section::make(__('general.Unit Features'))
                    ->schema([
                        Forms\Components\Repeater::make('features')
                            ->label(__('general.Features'))
                            ->schema([
                                Forms\Components\Select::make('feature_name')
                                    ->required()
                                    ->label(__('general.Feature Name'))
                                    ->options([
                                        'furnished' => __('general.Furnished'),
                                        'elevator' => __('general.Elevator'),
                                        'swimming_pool' => __('general.Swimming Pool'),
                                        'gym' => __('general.Gym'),
                                        'camera_security' => __('general.Camera Security'),
                                        'parking' => __('general.Parking'),
                                        'playground' => __('general.Playground'),
                                        'wifi' => __('general.WiFi'),
                                        'garden' => __('general.Garden'),
                                        'security' => __('general.Security'),
                                        'SMART_HOME' => __('general.Smart Home'),
                                        'fire_alarm' => __('general.Fire Alarm'),
                                        'central_air_conditioning' => __('general.Central Air Conditioning'),
                                        'heating' => __('general.Heating'),
                                        'fireplace' => __('general.Fireplace'),
                                       
                                    ]),
                                Forms\Components\Select::make('feature_value')
                                    ->required()
                                    ->label(__('general.Feature Value'))
                                    ->options([
                                        'yes' => __('general.YES'),
                                        'no' => __('general.NO'),
                                    ]),
                            ])
                            ->columns(2)
                            ->createItemButtonLabel(__('general.Add Feature')),
                    ]),
                 ///////////////////
                Forms\Components\Section::make(__('general.Images'))
                    ->schema([
                        self::unitImagesUpload(),
                    ]),

                Forms\Components\Section::make(__('general.Additional Details'))
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label(__('general.Notes'))
                            ->maxLength(65535),
                        Forms\Components\Select::make('status')
                            ->label(__('general.Status'))
                            ->options([
                                'available' => __('general.Available'),
                                'rented' => __('general.Rented'),
                                'under_maintenance' => __('general.Under Maintenance'),
                                'unavailable' => __('general.Unavailable'),
                                'reserved' => __('general.Reserved'),
                                'not_confirmed' => __('general.Not Confirmed'),
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
                    ->label(__('general.ID'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label(__('general.Unit Name'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit_number')
                    ->label(__('general.Unit Number'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label(__('general.Property'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit_type')
                    ->label(__('general.Unit Type'))
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
                        'apartment' => __('general.Apartment'),
                        'villa' => __('general.Villa'),
                        'warehouse' => __('general.Warehouse'),
                        'house' => __('general.House'),
                        'building' => __('general.Building'),
                        'studio' => __('general.Studio'),
                        'office' => __('general.Office'),
                        'shop' => __('general.Shop'),
                        default => ucfirst($state),
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('area')
                    ->label(__('general.Area (m²)'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('rental_price')
                    ->label(__('general.Rental Price'))
                    ->money('JOD')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label(__('general.Status'))
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
                        'available' => __('general.Available'),
                        'rented' => __('general.Rented'),
                        'under_maintenance' => __('general.Under Maintenance'),
                        'unavailable' => __('general.Unavailable'),
                        'reserved' => __('general.Reserved'),
                        'not_confirmed' => __('general.Not Confirmed'),
                        default => ucfirst($state),
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('general.Created At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('general.Updated At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label(__('general.Notes'))
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label(__('general.Property'))
                    ->relationship('property', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('unit_type')
                    ->label(__('general.Unit Type'))
                    ->options([
                        'apartment' => __('general.Apartment'),
                        'villa' => __('general.Villa'),
                        'warehouse' => __('general.Warehouse'),
                        'house' => __('general.House'),
                        'building' => __('general.Building'),
                        'studio' => __('general.Studio'),
                        'office' => __('general.Office'),
                        'shop' => __('general.Shop'),
                    ]),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('general.Status'))
                    ->options([
                        'available' => __('general.Available'),
                        'rented' => __('general.Rented'),
                        'under_maintenance' => __('general.Under Maintenance'),
                        'unavailable' => __('general.Unavailable'),
                        'reserved' => __('general.Reserved'),
                        'not_confirmed' => __('general.Not Confirmed'),
                    ]),
                    
                Tables\Filters\Filter::make('area_range')
                    ->label(__('general.Area Range'))
                    ->form([
                        Forms\Components\TextInput::make('area_from')
                            ->label(__('general.From (m²)'))
                            ->numeric(),
                        Forms\Components\TextInput::make('area_to')
                            ->label(__('general.To (m²)'))
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['area_from'], fn ($query, $area) => $query->where('area', '>=', $area))
                            ->when($data['area_to'], fn ($query, $area) => $query->where('area', '<=', $area));
                    }),
                    
                Tables\Filters\Filter::make('price_range')
                    ->label(__('general.Price Range'))
                    ->form([
                        Forms\Components\TextInput::make('price_from')
                            ->label(__('general.From (JOD)'))
                            ->numeric(),
                        Forms\Components\TextInput::make('price_to')
                            ->label(__('general.To (JOD)'))
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['price_from'], fn ($query, $price) => $query->where('rental_price', '>=', $price))
                            ->when($data['price_to'], fn ($query, $price) => $query->where('rental_price', '<=', $price));
                    }),
                    
                Tables\Filters\Filter::make('created_at')
                    ->label(__('general.Creation Date'))
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('general.From Date')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('general.To Date')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'], fn ($query, $date) => $query->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'], fn ($query, $date) => $query->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label(__('general.Export Data'))
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('general.Edit')),
                Tables\Actions\ViewAction::make()
                    ->label(__('general.View')),
                Tables\Actions\DeleteAction::make()
                    ->label(__('general.Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label(__('general.Delete Selected')),
                    FilamentExportBulkAction::make('export')
                        ->label(__('general.Export Selected')),
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
