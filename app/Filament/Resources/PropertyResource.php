<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Support\Facades\Filament;

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
                Forms\Components\Section::make('Property Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Property Name') // ✔️ تعديل التسمية لتكون أوضح
                            ->required(),
                        Forms\Components\Textarea::make('description')->label('Description'),
                        Forms\Components\Select::make('type1')
                            ->label('Primary Type') // ✔️ label معبر أكثر
                            ->options([
                                'building' => 'Building',
                                'villa' => 'Villa',
                                'house' => 'House',
                                'warehouse' => 'Warehouse',
                            ])
                            ->required(),
                        Forms\Components\Select::make('type2')
                            ->label('Usage Type') // ✔️ label معبر أكثر
                            ->options([
                                'residential' => 'Residential',
                                'commercial' => 'Commercial',
                                'industrial' => 'Industrial',
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('birth_date')->label('Construction Date'),
                        Forms\Components\TextInput::make('floors_count')->label('Floors Count')->numeric(),
                        Forms\Components\TextInput::make('floor_area')->label('Floor Area (m²)')->numeric(),
                        Forms\Components\TextInput::make('total_area')->label('Total Area (m²)')->numeric(),
                        Forms\Components\Select::make('acc_id')
                            ->label('acc_id')
                            ->relationship('acc', 'firstname')
                            ->required(),
                    ]),

                // 🗺️ قسم بيانات العنوان المرتبط
                Forms\Components\Section::make('Address Information')
                    ->schema([
                        Forms\Components\TextInput::make('address.country')->label('Country'),
                        Forms\Components\TextInput::make('address.governorate')->label('Governorate'),
                        Forms\Components\TextInput::make('address.city')->label('City'),
                        Forms\Components\TextInput::make('address.district')->label('District'),
                        Forms\Components\TextInput::make('address.building_number')->label('Building Number'),
                        Forms\Components\TextInput::make('address.plot_number')->label('Plot Number'),
                        Forms\Components\TextInput::make('address.basin_number')->label('Basin Number'),
                        Forms\Components\TextInput::make('address.property_number')->label('Property Number'),
                        Forms\Components\TextInput::make('address.street_name')->label('Street Name'),
                    ])
                    ->columns(2),
            ])
            ->columns(1)
        ;
    }

    // 🛠️ عند إنشاء سجل جديد
    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['address_data'] = $data['address'] ?? [];
        unset($data['address']);
        // سيتم إضافة property_id لاحقاً في afterCreate
        return $data;
    }

    // 🛠️ عند التعديل على السجل
    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['address_data'] = $data['address'] ?? [];
        unset($data['address']);
        // سيتم إضافة property_id لاحقاً في afterSave
        return $data;
    }

    public static function afterCreate($record, array $data): void
    {
        if (!empty($data['address_data'])) {
            $addressData = $data['address_data'];
            $addressData['property_id'] = $record->id;
            $record->address()->create($addressData);
        }
    }

    public static function afterSave($record, array $data): void
    {
        if (!empty($data['address_data'])) {
            $addressData = $data['address_data'];
            $addressData['property_id'] = $record->id;
            $record->address()->updateOrCreate(['property_id' => $record->id], $addressData);
        }
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