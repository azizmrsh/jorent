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
                                        'Area' => 'Area (sqm)',
                                        
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
                                        if (in_array($detailName, ['bedrooms', 'bathrooms', 'balconies', 'parking_spaces', 'floor', 'kitchen', 'area'])) {    
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Unit Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit_number')
                    ->label('Unit Number')
                    ->sortable()
                    ->searchable(),
                // Add table columns here
            ])
            ->filters([
                // Add table filters here
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
