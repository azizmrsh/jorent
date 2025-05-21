<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;

class PropertyResource extends Resource
{
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'Real Estate';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 🏠 قسم بيانات العقار
                Section::make('Property Information')
                    ->schema([
                        TextInput::make('name')
                            ->label('Property Name')
                            ->required(),
                        Forms\Components\Textarea::make('description')->label('Description'),
                        Forms\Components\Select::make('type1')
                            ->label('Primary Type')
                            ->options([
                                'building' => 'Building',
                                'villa' => 'Villa',
                                'house' => 'House',
                                'warehouse' => 'Warehouse',
                            ])
                            ->required(),
                        Forms\Components\Select::make('type2')
                            ->label('Usage Type')
                            ->options([
                                'residential' => 'Residential',
                                'commercial' => 'Commercial',
                                'industrial' => 'Industrial',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('birth_date')->label('Construction Date'),
                        TextInput::make('floors_count')->label('Floors Count')->numeric(),
                        TextInput::make('floor_area')->label('Floor Area (m²)')->numeric(),
                        TextInput::make('total_area')->label('Total Area (m²)')->numeric(),
                        Forms\Components\Select::make('acc_id')
                            ->label('acc_id')
                            ->relationship('acc', 'firstname')
                            ->required(),
                    ]),

                // 🗺️ قسم بيانات العنوان المرتبط
                Section::make('Address Information')
                    ->relationship('address')
                    ->schema([
                        TextInput::make('country')->label('Country'),
                        TextInput::make('governorate')->label('Governorate'),
                        TextInput::make('city')->label('City'),
                        TextInput::make('district')->label('District'),
                        TextInput::make('building_number')->label('Building Number'),
                        TextInput::make('plot_number')->label('Plot Number'),
                        TextInput::make('basin_number')->label('Basin Number'),
                        TextInput::make('property_number')->label('Property Number'),
                        TextInput::make('street_name')->label('Street Name'),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Property Name')->searchable(),
                Tables\Columns\TextColumn::make('type1')->label('Primary Type'),
                Tables\Columns\TextColumn::make('type2')->label('Usage Type'),
                Tables\Columns\TextColumn::make('full_address')->label('Full Address')->limit(50),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}
