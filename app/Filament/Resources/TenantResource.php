<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Filament\Resources\TenantResource\RelationManagers;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Staff Management (Adminstration)';
    protected static ?string $navigationLabel = 'Tenants';
    protected static ?string $label = 'Tenant';
    protected static ?string $pluralLabel = 'Tenants';
    protected static ?string $slug = 'tenants';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Personal Information
                Forms\Components\Fieldset::make('Personal Information')
                    ->schema([
                        Forms\Components\TextInput::make('firstname')
                            ->required()
                            ->label('First Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('midname')
                            ->label('Middle Name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('lastname')
                            ->label('Last Name')
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('birth_date')
                            ->label('Birth Date'),
                        Forms\Components\TextInput::make('nationality')
                            ->label('Nationality')
                            ->maxLength(255),
                    ]),

                // Contact Information
                Forms\Components\Fieldset::make('Contact Information')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('address')
                            ->label('Address')
                            ->maxLength(255),
                    ]),

                // Profile Information
                Forms\Components\Fieldset::make('Profile Information')
                    ->schema([
                        Forms\Components\FileUpload::make('profile_photo')
                            ->label('Profile Photo')
                            ->image()
                            ->directory('uploads/images')
                            ->maxSize(1024),
                        Forms\Components\TextInput::make('password')
                            ->required()
                            ->label('Password')
                            ->password()
                            ->maxLength(255)
                            ->dehydrated(fn ($state) => filled($state))
                            ->visible(fn (string $context) => in_array($context, ['create', 'edit'])),
                        Forms\Components\TextInput::make('status')  
                            ->required()
                            ->label('Status')
                            ->maxLength(255)
                            ->default('active'),
                    ]),

                // Document Information
                Forms\Components\Fieldset::make('Document Information')
                    ->schema([
                        Forms\Components\Select::make('document_type')
                            ->label('Document Type')
                            ->options([
                                'passport' => 'Passport',
                                'id_card' => 'ID Card',
                                'driver_license' => 'Driver License',
                                'residency_permit' => 'Residency Permit',
                                'other' => 'Other',
                            ])
                            ->default('passport'),
                        Forms\Components\TextInput::make('document_number')     
                            ->label('Document Number')
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('document_photo')
                            ->label('Document Photo')
                            ->image()
                            ->directory('uploads/images')
                            ->maxSize(1024),
                    ]),

                // Employment Information
                Forms\Components\Fieldset::make('Tented by')
                    ->schema([
                        Forms\Components\DatePicker::make('hired_date')
                            ->default(now())
                            ->label('Hired Date')
                            ->disabled(),
                        Forms\Components\TextInput::make('hired_by')
                            ->default(optional(auth()->user())->name)
                            ->label('Tented by')
                            ->maxLength(255)
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('firstname')
                    ->label('First Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('midname')
                    ->label('Middle Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->label('Last Name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Birth Date')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('profile_photo')
                    ->label('Profile Photo')
                    ->circular()
                    ->size(40)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('document_type')
                    ->label('Document Type')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('document_number')
                    ->label('Document Number')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\ImageColumn::make('document_photo')
                    ->label('Document Photo')
                    ->circular()
                    ->size(40)
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('nationality')
                    ->label('Nationality')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('hired_date')
                    ->label('Hired Date')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('hired_by')
                    ->label('Hired By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('firstname')
                    ->query(fn (Builder $query): Builder => $query->where('firstname', '!=', ''))
                    ->label('First Name'),
                Tables\Filters\Filter::make('midname')
                    ->query(fn (Builder $query): Builder => $query->where('midname', '!=', ''))
                    ->label('Middle Name'),
                Tables\Filters\Filter::make('lastname')
                    ->query(fn (Builder $query): Builder => $query->where('lastname', '!=', ''))
                    ->label('Last Name'),
                Tables\Filters\Filter::make('email')
                    ->query(fn (Builder $query): Builder => $query->where('email', '!=', ''))
                    ->label('Email'),
                Tables\Filters\Filter::make('phone')
                    ->query(fn (Builder $query): Builder => $query->where('phone', '!=', ''))
                    ->label('Phone'),
                Tables\Filters\Filter::make('address')
                    ->query(fn (Builder $query): Builder => $query->where('address', '!=', ''))
                    ->label('Address'),
                Tables\Filters\Filter::make('birth_date')
                    ->query(fn (Builder $query): Builder => $query->where('birth_date', '!=', ''))
                    ->label('Birth Date'),
                Tables\Filters\Filter::make('status')
                    ->query(fn (Builder $query): Builder => $query->where('status', '!=', ''))
                    ->label('Status'),
                Tables\Filters\Filter::make('document_type')
                    ->query(fn (Builder $query): Builder => $query->where('document_type', '!=', ''))
                    ->label('Document Type'),
                Tables\Filters\Filter::make('document_number')
                    ->query(fn (Builder $query): Builder => $query->where('document_number', '!=', ''))
                    ->label('Document Number'),
                Tables\Filters\Filter::make('nationality')
                    ->query(fn (Builder $query): Builder => $query->where('nationality', '!=', ''))
                    ->label('Nationality'),
                Tables\Filters\Filter::make('hired_date')
                    ->query(fn (Builder $query): Builder => $query->where('hired_date', '!=', ''))
                    ->label('Hired Date'),
                Tables\Filters\Filter::make('hired_by')
                    ->query(fn (Builder $query): Builder => $query->where('hired_by', '!=', ''))
                    ->label('Hired By'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\ContractsRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\CustomInvoicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'view' => Pages\ViewTenant::route('/{record}'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}