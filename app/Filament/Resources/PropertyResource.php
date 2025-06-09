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
    protected static ?string $navigationLabel = null;
    protected static ?string $navigationGroup = 'Real Estate ';
    protected static ?string $label = null;
    protected static ?string $pluralLabel = null;
    protected static ?string $slug = 'properties';

    public static function getNavigationLabel(): string
    {
        return __('general.Properties');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Property');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Properties');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // 🏠 قسم بيانات العقار
                Section::make(__('general.Property Information'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('general.Property Name'))
                            ->required(),
                        
                        // 📸 رفع صورة العقار
                        self::propertyImageUpload(),
                        Forms\Components\Textarea::make('description')->label(__('general.Description')),
                        Forms\Components\Select::make('type1')
                            ->label(__('general.Primary Type'))
                            ->options([
                                'building' => __('general.Building'),
                                'villa' => __('general.Villa'),
                                'house' => __('general.House'),
                                'warehouse' => __('general.Warehouse'),
                            ])
                            ->required(),
                        Forms\Components\Select::make('type2')
                            ->label(__('general.Usage Type'))
                            ->options([
                                'residential' => __('general.Residential'),
                                'commercial' => __('general.Commercial'),
                                'industrial' => __('general.Industrial'),
                            ])
                            ->required(),
                        Forms\Components\DatePicker::make('birth_date')->label(__('general.Construction Date')),
                        TextInput::make('floors_count')->label(__('general.Floors Count'))->numeric(),
                        TextInput::make('floor_area')->label(__('general.Floor Area (m²)'))->numeric(),
                        TextInput::make('total_area')->label(__('general.Total Area (m²)'))->numeric(),
                        Forms\Components\Select::make('acc_id')
                            ->label(__('general.Account Manager'))
                            ->relationship('acc', 'firstname')
                            ->required(),
                    ]),

                // 🗺️ قسم بيانات العنوان المرتبط
                Section::make(__('general.Address Information'))
                    ->relationship('address')
                    ->schema([
                        TextInput::make('country')->label(__('general.Country')),
                        TextInput::make('governorate')->label(__('general.Governorate')),
                        TextInput::make('city')->label(__('general.City')),
                        TextInput::make('district')->label(__('general.District')),
                        TextInput::make('building_number')->label(__('general.Building Number')),
                        TextInput::make('plot_number')->label(__('general.Plot Number')),
                        TextInput::make('basin_number')->label(__('general.Basin Number')),
                        TextInput::make('property_number')->label(__('general.Property Number')),
                        TextInput::make('street_name')->label(__('general.Street Name')),
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
                    ->label(__('general.Image'))
                    ->width(80)
                    ->height(60)
                    ->defaultImageUrl('/images/no-image.svg')
                    ->circular(false)
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('id')
                    ->label(__('general.Property ID'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('general.Property Name'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type1')
                    ->label(__('general.Primary Type'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('type2')
                    ->label(__('general.Usage Type'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('acc.firstname')
                    ->label(__('general.Account Manager'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('floors_count')
                    ->label(__('general.Floors'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('total_area')
                    ->label(__('general.Total Area (m²)'))
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label(__('general.Construction Date'))
                    ->date()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address.city')
                    ->label(__('general.City'))
                    ->limit(50)
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('general.Created'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // 🔍 فلتر النص
                Tables\Filters\Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label(__('general.Property Name'))
                            ->placeholder(__('general.Search by property name...'))
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['name'],
                            fn (Builder $query, $name): Builder => $query->where('name', 'like', '%' . $name . '%')
                        );
                    })
                    ->label(__('general.Property Name')),

                // 🏢 فلتر النوع الأساسي
                Tables\Filters\SelectFilter::make('type1')
                    ->label(__('general.Primary Type'))
                    ->options([
                        'building' => __('general.Building'),
                        'villa' => __('general.Villa'),
                        'house' => __('general.House'),
                        'warehouse' => __('general.Warehouse'),
                    ])
                    ->multiple()
                    ->placeholder(__('general.Select property types')),

                // 🎯 فلتر نوع الاستخدام
                Tables\Filters\SelectFilter::make('type2')
                    ->label(__('general.Usage Type'))
                    ->options([
                        'residential' => __('general.Residential'),
                        'commercial' => __('general.Commercial'),
                        'industrial' => __('general.Industrial'),
                    ])
                    ->multiple()
                    ->placeholder(__('general.Select usage types')),

                // 🏗️ فلتر عدد الطوابق
                Tables\Filters\Filter::make('floors_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('floors_from')
                                    ->label(__('general.Floors From'))
                                    ->numeric()
                                    ->placeholder(__('general.Min floors')),
                                Forms\Components\TextInput::make('floors_to')
                                    ->label(__('general.Floors To'))
                                    ->numeric()
                                    ->placeholder(__('general.Max floors')),
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
                    ->label(__('general.Floors Count')),

                // 📐 فلتر المساحة
                Tables\Filters\Filter::make('area_range')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('area_from')
                                    ->label(__('general.Area From (m²)'))
                                    ->numeric()
                                    ->placeholder(__('general.Min area')),
                                Forms\Components\TextInput::make('area_to')
                                    ->label(__('general.Area To (m²)'))
                                    ->numeric()
                                    ->placeholder(__('general.Max area')),
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
                    ->label(__('general.Total Area (m²)')),

                // 📅 فلتر تاريخ البناء
                Tables\Filters\Filter::make('construction_date')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_from')
                                    ->label(__('general.Construction From'))
                                    ->placeholder(__('general.Start date')),
                                Forms\Components\DatePicker::make('date_to')
                                    ->label(__('general.Construction To'))
                                    ->placeholder(__('general.End date')),
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
                    ->label(__('general.Construction Date')),

                // 👤 فلتر مدير الحساب
                Tables\Filters\SelectFilter::make('acc_id')
                    ->relationship('acc', 'firstname')
                    ->label(__('general.Account Manager'))
                    ->multiple()
                    ->placeholder(__('general.Select account managers')),

                // 📍 فلتر المدينة
                Tables\Filters\SelectFilter::make('city')
                    ->relationship('address', 'city')
                    ->label(__('general.City'))
                    ->multiple()
                    ->placeholder(__('general.Select cities')),
            ])
            ->headerActions([

                FilamentExportHeaderAction::make('export')
                    ->label(__('general.Export'))
                    ->fileName('properties-export')
                    ->defaultFormat('xlsx')
                    ->defaultPageOrientation('landscape')
                    ->disablePreview(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label(__('general.View')),
                Tables\Actions\EditAction::make()->label(__('general.Edit')),
                Tables\Actions\DeleteAction::make()->label(__('general.Delete')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('general.Delete Selected')),
                    FilamentExportBulkAction::make('export-selected')
                        ->label(__('general.Export Selected'))
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