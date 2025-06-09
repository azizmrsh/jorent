<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccResource\Pages;
use App\Models\Acc;
use App\Traits\FileUploadTrait;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;


class AccResource extends Resource
{
    use FileUploadTrait;
    
    protected static ?string $model = Acc::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Real Estate Management';
    protected static ?string $slug = 'property-managers';

    public static function getNavigationLabel(): string
    {
        return __('general.Property Managers');
    }
    
    public static function getModelLabel(): string
    {
        return __('general.Property Manager');
    }
    
    public static function getPluralModelLabel(): string
    {
        return __('general.Property Managers');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Fieldset::make(__('general.Personal Information'))->schema([
                Forms\Components\TextInput::make('firstname')->label(__('general.First Name'))->required()->maxLength(255),
                Forms\Components\TextInput::make('midname')->label(__('general.Middle Name'))->maxLength(255),
                Forms\Components\TextInput::make('lastname')->label(__('general.Last Name'))->maxLength(255),
                Forms\Components\DatePicker::make('birth_date')->label(__('general.Birth Date')),
                Forms\Components\TextInput::make('nationality')->label(__('general.Nationality'))->maxLength(255),
            ]),
            Forms\Components\Fieldset::make(__('general.Contact Information'))->schema([
                Forms\Components\TextInput::make('email')
                    ->label(__('general.Email'))
                    ->email()
                    ->maxLength(255)
                    ->required()
                    ->unique(table: 'accs', column: 'email', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => __('general.This email address is already in use. Please choose another one'),
                        'email' => __('general.Please enter a valid email address'),
                        'required' => __('general.The email field is required'),
                    ]),
                Forms\Components\TextInput::make('phone')->label(__('general.Phone'))->maxLength(255),
                Forms\Components\TextInput::make('address')->label(__('general.Address'))->maxLength(255),
            ]),
            Forms\Components\Fieldset::make(__('general.Profile Information'))->schema([
                self::profilePhotoUpload(),
                Forms\Components\TextInput::make('password')->label(__('general.Password'))->password()->maxLength(255)->required()->dehydrated(fn ($state) => filled($state))->visible(fn (string $context) => in_array($context, ['create', 'edit'])),
                Forms\Components\TextInput::make('status')->label(__('general.Status'))->required()->maxLength(255)->default('active'),
            ]),
            Forms\Components\Fieldset::make(__('general.Document Information'))->schema([
                Forms\Components\Select::make('document_type')->label(__('general.Document Type'))->options([
                    'passport' => __('general.Passport'),
                    'id_card' => __('general.ID Card'),
                    'driver_license' => __('general.Driver License'),
                    'residency_permit' => __('general.Residency Permit'),
                    'other' => __('general.Other'),
                ])->default('passport'),
                Forms\Components\TextInput::make('document_number')->label(__('general.Document Number'))->maxLength(255),
                self::documentPhotoUpload(),
            ]),
            Forms\Components\Fieldset::make(__('general.Employment Information'))->schema([
                Forms\Components\DatePicker::make('hired_date')->default(now())->label(__('general.Hired Date'))->disabled(),
                Forms\Components\TextInput::make('hired_by')->default(\Illuminate\Support\Facades\Auth::user()?->name)->label(__('general.Hired By'))->maxLength(255)->disabled(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            //    Tables\Columns\ImageColumn::make('profile_photo')
            //        ->label(__('general.Profile Photo'))
            //        ->circular()
            //        ->size(50)
            //        ->defaultImageUrl('data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" fill="#e5e7eb"><circle cx="50" cy="50" r="50"/><circle cx="50" cy="35" r="15" fill="#9ca3af"/><ellipse cx="50" cy="75" rx="20" ry="15" fill="#9ca3af"/></svg>'))
            //        ->disk('public')
            //        ->visibility('public')
            //        ->sortable()
            //        ->toggleable(),
                Tables\Columns\TextColumn::make('firstname')->label(__('general.First Name'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('midname')->label(__('general.Middle Name'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('lastname')->label(__('general.Last Name'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('email')->label(__('general.Email'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('phone')->label(__('general.Phone'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('address')->label(__('general.Address'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('birth_date')->label(__('general.Birth Date'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('status')->label(__('general.Status'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('document_type')->label(__('general.Document Type'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('document_number')->label(__('general.Document Number'))->searchable()->sortable()->toggleable(),
                //Tables\Columns\ImageColumn::make('document_photo')->label(__('general.Document Photo'))->circular()->size(40)->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('nationality')->label(__('general.Nationality'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('hired_date')->label(__('general.Hired Date'))->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('hired_by')->label(__('general.Hired By'))->searchable()->sortable()->toggleable(),
            ])
    ->filters([
        Tables\Filters\Filter::make('firstname')->label(__('general.First Name')),
        Tables\Filters\Filter::make('midname')->label(__('general.Middle Name')),
        Tables\Filters\Filter::make('lastname')->label(__('general.Last Name')),
        Tables\Filters\Filter::make('email')->label(__('general.Email')),
        Tables\Filters\Filter::make('phone')->label(__('general.Phone')),
        Tables\Filters\Filter::make('address')->label(__('general.Address')),
        Tables\Filters\Filter::make('birth_date')->label(__('general.Birth Date')),
        Tables\Filters\SelectFilter::make('status')->label(__('general.Status'))->options([
            'active' => __('general.Active'),
            'inactive' => __('general.Inactive'),
        ]),
        Tables\Filters\SelectFilter::make('document_type')->label(__('general.Document Type'))->options([
            'passport' => __('general.Passport'),
            'id_card' => __('general.ID Card'),
            'driver_license' => __('general.Driver License'),
            'residency_permit' => __('general.Residency Permit'),
            'other' => __('general.Other'),
        ]),
    Tables\Filters\Filter::make('document_number')->label(__('general.Document Number')),
    Tables\Filters\Filter::make('nationality')->label(__('general.Nationality')),
    Tables\Filters\TernaryFilter::make('profile_photo')
        ->label(__('general.Has Profile Photo'))
        ->trueLabel(__('general.With Photo'))
        ->falseLabel(__('general.Without Photo'))
        ->queries(
            true: fn (Builder $query) => $query->whereNotNull('profile_photo')->where('profile_photo', '!=', ''),
            false: fn (Builder $query) => $query->whereNull('profile_photo')->orWhere('profile_photo', ''),
        ),
    Tables\Filters\TernaryFilter::make('document_photo')
        ->label(__('general.Has Document Photo'))
        ->trueLabel(__('general.With Photo'))
        ->falseLabel(__('general.Without Photo'))
        ->queries(
            true: fn (Builder $query) => $query->whereNotNull('document_photo')->where('document_photo', '!=', ''),
            false: fn (Builder $query) => $query->whereNull('document_photo')->orWhere('document_photo', ''),
        ),
        
    Tables\Filters\Filter::make('hired_date')->label(__('general.Hired Date')),
    Tables\Filters\Filter::make('hired_by')->label(__('general.Hired By')),
])

            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label(__('general.Export'))
                    ->fileName('property-managers-export')
                    ->defaultFormat('xlsx')
                    ->defaultPageOrientation('landscape')
                    ->disablePreview(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label(__('general.Edit')),
                Tables\Actions\DeleteAction::make()->label(__('general.Delete')),
                Tables\Actions\ViewAction::make()->label(__('general.View')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label(__('general.Delete Selected')),
                    FilamentExportBulkAction::make('export-selected')
                        ->label(__('general.Export Selected'))
                        ->fileName('property-managers-selected')
                        ->defaultFormat('pdf')
                        ->disablePreview(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccs::route('/'),
            'create' => Pages\CreateAcc::route('/create'),
            'view' => Pages\ViewAcc::route('/{record}'),
            'edit' => Pages\EditAcc::route('/{record}/edit'),
        ];
    }
}
