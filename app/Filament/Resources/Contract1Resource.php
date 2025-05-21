<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Contract1Resource\Pages;
use App\Models\Contract1;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

use Saade\FilamentAutograph\Forms\Components\SignaturePad;

class Contract1Resource extends Resource
{
    protected static ?string $model = Contract1::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Rental Management';
    protected static ?string $navigationLabel = 'Contract1s';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Landlord Info')->schema([
                Forms\Components\TextInput::make('landlord_name')->required(),
            ])->columns(2),

            Forms\Components\Section::make('Tenant Info')->schema([
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->required(),
            ])->columns(2),

            Forms\Components\Section::make('Property & Unit')->schema([
                Forms\Components\Select::make('property_id')
                    ->options(Property::all()->pluck('name', 'id'))
                    ->required()
                    ->label('Property')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $property = Property::with('address')->find($state);
                        if ($property?->address) {
                            $set('governorate', $property->address->governorate);
                            $set('city', $property->address->city);
                            $set('district', $property->address->district);
                            $set('building_number', $property->address->building_number);
                            $set('plot_number', $property->address->plot_number);
                            $set('basin_number', $property->address->basin_number);
                            $set('property_number', $property->address->property_number);
                            $set('street_name', $property->address->street_name);
                        }
                        $set('unit_id', null);
                    }),
                Forms\Components\Select::make('unit_id')
                    ->options(fn (callable $get) =>
                        Unit::where('property_id', $get('property_id'))->pluck('name', 'id')
                    )
                    ->required()
                    ->label('Unit'),

                Forms\Components\TextInput::make('governorate')->label('Governorate')->readOnly(),
                Forms\Components\TextInput::make('city')->label('City')->readOnly(),
                Forms\Components\TextInput::make('district')->label('District')->readOnly(),
                Forms\Components\TextInput::make('building_number')->label('Building Number')->readOnly(),
                Forms\Components\TextInput::make('plot_number')->label('Plot Number')->readOnly(),
                Forms\Components\TextInput::make('basin_number')->label('Basin Number')->readOnly(),
                Forms\Components\TextInput::make('property_number')->label('Property Number')->readOnly(),
                Forms\Components\TextInput::make('street_name')->label('Street Name')->readOnly(),
            ])->columns(3),

            Forms\Components\Section::make('Contract Details')->schema([
                Forms\Components\DatePicker::make('start_date')->required(),
                Forms\Components\DatePicker::make('end_date')->required(),
                Forms\Components\DatePicker::make('due_date')->label('Due Date'),
                Forms\Components\TextInput::make('rent_amount')->numeric()->required(),
                Forms\Components\Textarea::make('terms_and_conditions_extra'),
                Forms\Components\Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->reactive()
                    ->afterStateHydrated(function (callable $set, callable $get) {
                        $startDate = $get('start_date');
                        $endDate = $get('end_date');

                        if ($startDate && $endDate && now()->between($startDate, $endDate)) {
                            $set('status', 'active');
                        } else {
                            $set('status', 'inactive');
                        }
                    }),
            ])->columns(3),

            Forms\Components\Section::make('Signatures')->schema([
                SignaturePad::make('tenant_signature')->label('Tenant Signature')->required(),
                SignaturePad::make('landlord_signature')->label('Landlord Signature')->required(),
                SignaturePad::make('witness1_signature')->label('Witness 1 Signature')->required(),
                SignaturePad::make('witness2_signature')->label('Witness 2 Signature')->required(),
            ])->columns(4),

            Forms\Components\Section::make('Meta Info')->schema([
                Forms\Components\DatePicker::make('hired_date')
                    ->default(now())->readOnly(),
                Forms\Components\TextInput::make('hired_by')
                    ->default(fn () => Auth::user()?->name)
                    ->readOnly(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('landlord_name')->searchable(),
                Tables\Columns\TextColumn::make('tenant.firstname')->label('Tenant'),
                Tables\Columns\TextColumn::make('unit.name')->label('Unit'),
                Tables\Columns\TextColumn::make('property.name')->label('Property'),
                Tables\Columns\TextColumn::make('start_date'),
                Tables\Columns\TextColumn::make('end_date'),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContract1s::route('/'),
            'create' => Pages\CreateContract1::route('/create'),
            'edit' => Pages\EditContract1::route('/{record}/edit'),
           // 'view' => Pages\ViewContract1::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }
}
