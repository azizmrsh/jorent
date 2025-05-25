// ✅ إضافة دعم التصدير بثلاث صيغ: Excel, CSV, PDF
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
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Staff Management (Adminstration)';
    protected static ?string $navigationLabel = 'Onwer and Managers' ;
    protected static ?string $label = 'Managers ';
    protected static ?string $pluralLabel = 'Onwer and Managers Information';
    protected static ?string $slug = 'managers'; 
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required()->label('First Name')->maxLength(255),
                Forms\Components\TextInput::make('midname')->required()->label('Middle Name')->maxLength(255),
                Forms\Components\TextInput::make('lastname')->required()->label('Last Name')->maxLength(255),
                Forms\Components\TextInput::make('role')->required()->label('Role')->maxLength(255)->default('user'),
                Forms\Components\TextInput::make('status')->required()->label('Status')->maxLength(255)->default('active'),
                Forms\Components\TextInput::make('email')->required()->label('Email')->email()->maxLength(255),
                Forms\Components\TextInput::make('phone')->label('Phone')->tel()->maxLength(255),
                Forms\Components\TextInput::make('address')->required()->label('Address')->maxLength(255),
                Forms\Components\DatePicker::make('birth_date')->label('Birth Date'),
                Forms\Components\FileUpload::make('profile_photo')->label('Profile Image')->image()->directory('uploads/images')->maxSize(1024),
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
                Tables\Columns\TextColumn::make('name')->label('First Name')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('midname')->label('Middle Name')->sortable()->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('lastname')->label('Last Name')->sortable()->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('role')->label('Role')->sortable()->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->sortable()->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->sortable()->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('phone')->label('Phone')->sortable()->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('address')->label('Address')->sortable()->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('birth_date')->label('Birth Date')->sortable()->toggleable()->searchable(),
                Tables\Columns\TextColumn::make('profile_photo')->label('Profile Photo')->sortable()->toggleable()->searchable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('name')->query(fn (Builder $query): Builder => $query->where('name', '!=', ''))->label('First Name'),
                Tables\Filters\Filter::make('midname')->query(fn (Builder $query): Builder => $query->where('midname', '!=', ''))->label('Middle Name'),
                Tables\Filters\Filter::make('lastname')->query(fn (Builder $query): Builder => $query->where('lastname', '!=', ''))->label('Last Name'),
                Tables\Filters\Filter::make('role')->query(fn (Builder $query): Builder => $query->where('role', '!=', ''))->label('Role'),
                Tables\Filters\Filter::make('status')->query(fn (Builder $query): Builder => $query->where('status', '!=', ''))->label('Status'),
                Tables\Filters\Filter::make('email')->query(fn (Builder $query): Builder => $query->where('email', '!=', ''))->label('Email'),
                Tables\Filters\Filter::make('phone')->query(fn (Builder $query): Builder => $query->where('phone', '!=', ''))->label('Phone'),
                Tables\Filters\Filter::make('address')->query(fn (Builder $query): Builder => $query->where('address', '!=', ''))->label('Address'),
                Tables\Filters\Filter::make('birth_date')->query(fn (Builder $query): Builder => $query->where('birth_date', '!=', ''))->label('Birth Date')
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export')
                    ->fileName('users-export')
                    ->defaultFormat('xlsx')
                    ->formatOptions(['xlsx', 'csv'])
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
                        ->fileName('users-selected')
                        ->defaultFormat('pdf')
                        ->formatOptions(['pdf', 'xlsx', 'csv'])
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
