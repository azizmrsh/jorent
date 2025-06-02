<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PropertyResource\Pages;
use App\Models\Property;
use App\Traits\FileUploadTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class PropertyResource extends Resource
{
    use FileUploadTrait;
    
    protected static ?string $model = Property::class;

    protected static ?string $navigationIcon = 'heroicon-o-home-modern';
    protected static ?string $navigationLabel = 'Properties';
    protected static ?string $navigationGroup = 'Real Estate ';
    protected static ?string $label = 'Property';
    protected static ?string $pluralLabel = 'Properties';
    protected static ?string $slug = 'properties';

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
                        
                        // 📸 رفع صورة العقار
                        self::propertyImageUpload(),
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
                            ->label('Account Manager')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 📸 عرض صورة العقار
                ImageColumn::make('image_path')
                    ->label('Image')
                    ->width(80)
                    ->height(60)
                    ->defaultImageUrl('/images/no-image.svg')
                    ->circular(false)
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Property Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type1')
                    ->label('Primary Type')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type2')
                    ->label('Usage Type')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('acc.firstname')
                    ->label('Account Manager')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('floors_count')
                    ->label('Floors')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_area')
                    ->label('Total Area (m²)')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Construction Date')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address.city')
                    ->label('City')
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 🔍 فلتر النص
                Tables\Filters\Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Property Name')
                            ->placeholder('Search by property name...')
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $name): Builder => $query->where('name', 'like', '%' . $name . '%')
                        );
                    })
                    ->label('🏠 Property Name'),

                // 🏢 فلتر النوع الأساسي
                Tables\Filters\SelectFilter::make('type1')
                    ->label('🏗️ Primary Type')
                    ->options([
                        'building' => '🏢 Building',
                        'villa' => '🏰 Villa',
                        'house' => '🏡 House',
                        'warehouse' => '🏪 Warehouse',
                    ])
                    ->multiple()
                    ->placeholder('Select property types'),

                // 🎯 فلتر نوع الاستخدام
                Tables\Filters\SelectFilter::make('type2')
                    ->label('🎯 Usage Type')
                    ->options([
                        'residential' => '🏠 Residential',
                        'commercial' => '🏢 Commercial',
                        'industrial' => '🏭 Industrial',
                    ])
                    ->multiple()
                    ->placeholder('Select usage types'),

                // 🏗️ فلتر عدد الطوابق
                Tables\Filters\Filter::make('floors_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('floors_from')
                                    ->label('Floors From')
                                    ->numeric()
                                    ->placeholder('Min floors'),
                                Forms\Components\TextInput::make('floors_to')
                                    ->label('Floors To')
                                    ->numeric()
                                    ->placeholder('Max floors'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['floors_from'],
                                fn (Builder $query, $floors): Builder => $query->where('floors_count', '>=', $floors)
                            )
                            ->when(
                                $data['floors_to'],
                                fn (Builder $query, $floors): Builder => $query->where('floors_count', '<=', $floors)
                            );
                    })
                    ->label('🏗️ Floors Count'),

                // 📐 فلتر المساحة
                Tables\Filters\Filter::make('area_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('area_from')
                                    ->label('Area From (m²)')
                                    ->numeric()
                                    ->placeholder('Min area'),
                                Forms\Components\TextInput::make('area_to')
                                    ->label('Area To (m²)')
                                    ->numeric()
                                    ->placeholder('Max area'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['area_from'],
                                fn (Builder $query, $area): Builder => $query->where('total_area', '>=', $area)
                            )
                            ->when(
                                $data['area_to'],
                                fn (Builder $query, $area): Builder => $query->where('total_area', '<=', $area)
                            );
                    })
                    ->label('📐 Total Area'),

                // 📅 فلتر تاريخ البناء
                Tables\Filters\Filter::make('construction_date')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_from')
                                    ->label('Construction From')
                                    ->placeholder('Start date'),
                                Forms\Components\DatePicker::make('date_to')
                                    ->label('Construction To')
                                    ->placeholder('End date'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date_from'],
                                fn (Builder $query, $date): Builder => $query->where('birth_date', '>=', $date)
                            )
                            ->when(
                                $data['date_to'],
                                fn (Builder $query, $date): Builder => $query->where('birth_date', '<=', $date)
                            );
                    })
                    ->label('📅 Construction Date'),

                // 👤 فلتر مدير الحساب
                Tables\Filters\SelectFilter::make('acc_id')
                    ->relationship('acc', 'firstname')
                    ->label('👤 Account Manager')
                    ->multiple()
                    ->placeholder('Select account managers'),

                // 📍 فلتر المدينة
                Tables\Filters\SelectFilter::make('city')
                    ->relationship('address', 'city')
                    ->label('📍 City')
                    ->multiple()
                    ->placeholder('Select cities'),
            ])
            ->headerActions([

                FilamentExportHeaderAction::make('export')
                    ->label('Export')
                    ->fileName('properties-export')
                    ->defaultFormat('xlsx')
                    ->defaultPageOrientation('landscape')
                    ->disablePreview(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export-selected')
                        ->label('Export Selected')
                        ->fileName('properties-selected')
                        ->defaultFormat('pdf')
                        ->disablePreview(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProperties::route('/'),
            'create' => Pages\CreateProperty::route('/create'),
            'view' => Pages\ViewProperty::route('/{record}'),
            'edit' => Pages\EditProperty::route('/{record}/edit'),
        ];
    }
}