<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Staff Management (Adminstration)';
    protected static ?string $navigationLabel = 'Users' ;
    protected static ?string $label = 'User ';
    protected static ?string $pluralLabel = 'User Information';
    protected static ?string $slug = 'users'; 
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('First Name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('midname')
                    ->required()
                    ->label('Middle Name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('lastname')
                    ->required()
                    ->label('Last Name')
                    ->maxLength(255),
                Forms\Components\TextInput::make('role')
                    ->required()
                    ->label('Role')
                    ->maxLength(255)
                    ->default('user'), // user, admin, superadmin
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->label('Status')
                    ->maxLength(255)
                    ->default('active'), // active, inactive, banned
                Forms\Components\TextInput::make('email')
                    ->required()
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                Forms\Components\TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('address')
                    ->required()
                    ->label('Address')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('birth_date')
                    ->label('Birth Date'),
                Forms\Components\FileUpload::make('profile_photo')
                    ->label('Profile Image')
                    ->image()
                    ->directory('uploads/images')
                    ->maxSize(1024), // Optional size limit in KB
                Forms\Components\TextInput::make('password')
                    ->required()
                    ->label('Password')
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn ($state) => bcrypt($state))
                    ->confirmed('password_confirmation')
                    ->label('Password Confirmation')
                    ->dehydrated(fn ($state) => ! blank($state)),
                Forms\Components\TextInput::make('password_confirmation')
                    ->required()
                    ->label('Password Confirmation')
                    ->password()
                    ->minLength(8)
                    ->maxLength(255)
                    ->dehydrated(fn ($state) => ! blank($state)),

                

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('name')
                    ->label('First Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('midname')
                    ->label('Middle Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('lastname')
                    ->label('Last Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->label('Address')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Birth Date')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('profile_photo')
                    ->label('Profile Photo')
                    ->sortable()
                    ->searchable(),


            ])
            ->filters([
                Tables\Filters\Filter::make('name')
                    ->query(fn (Builder $query): Builder => $query->where('firstname', '!=', ''))
                    ->label('First Name'),
                Tables\Filters\Filter::make('MIDNAME')
                    ->query(fn (Builder $query): Builder => $query->where('midname', '!=', ''))
                    ->label('Middle Name'),
                Tables\Filters\Filter::make('LASTNAME')
                    ->query(fn (Builder $query): Builder => $query->where('lastname', '!=', ''))
                    ->label('Last Name'),
                Tables\Filters\Filter::make('ROLE')
                    ->query(fn (Builder $query): Builder => $query->where('role', '!=', ''))
                    ->label('Role'),
                Tables\Filters\Filter::make('STATUS')
                    ->query(fn (Builder $query): Builder => $query->where('status', '!=', ''))
                    ->label('Status'),
                Tables\Filters\Filter::make('EMAIL')
                    ->query(fn (Builder $query): Builder => $query->where('email', '!=', ''))
                    ->label('Email'),
                Tables\Filters\Filter::make('PHONE')
                    ->query(fn (Builder $query): Builder => $query->where('phone', '!=', ''))
                    ->label('Phone'),
                Tables\Filters\Filter::make('ADDRESS')
                    ->query(fn (Builder $query): Builder => $query->where('address', '!=', ''))
                    ->label('Address'),
                Tables\Filters\Filter::make('BIRTH_DATE')
                    ->query(fn (Builder $query): Builder => $query->where('birth_date', '!=', ''))
                    ->label('Birth Date')


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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
