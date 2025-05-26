<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccResource\Pages;
use App\Models\Acc;
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
    protected static ?string $model = Acc::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Staff Management (Adminstration)';
    protected static ?string $navigationLabel = 'Accounts Manager';
    protected static ?string $label = 'Accounts Manager';
    protected static ?string $pluralLabel = 'Accounts Manager';
    protected static ?string $slug = 'accs';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Fieldset::make('Personal Information')->schema([
                Forms\Components\TextInput::make('firstname')->label('First Name')->required()->maxLength(255),
                Forms\Components\TextInput::make('midname')->label('Middle Name')->maxLength(255),
                Forms\Components\TextInput::make('lastname')->label('Last Name')->maxLength(255),
                Forms\Components\DatePicker::make('birth_date')->label('Birth Date'),
                Forms\Components\TextInput::make('nationality')->label('Nationality')->maxLength(255),
            ]),
            Forms\Components\Fieldset::make('Contact Information')->schema([
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255)
                    ->required()
                    ->unique(table: 'accs', column: 'email', ignoreRecord: true)
                    ->validationMessages([
                        'unique' => 'هذا البريد الإلكتروني مستخدم مسبقاً. يرجى اختيار بريد آخر.',
                        'email' => 'يرجى إدخال بريد إلكتروني صحيح.',
                        'required' => 'البريد الإلكتروني مطلوب.',
                    ]),
                Forms\Components\TextInput::make('phone')->label('Phone')->maxLength(255),
                Forms\Components\TextInput::make('address')->label('Address')->maxLength(255),
            ]),
            Forms\Components\Fieldset::make('Profile Information')->schema([
                Forms\Components\FileUpload::make('profile_photo')->label('Profile Photo')->image()->directory('uploads/images')->maxSize(1024),
                Forms\Components\TextInput::make('password')->label('Password')->password()->maxLength(255)->required()->dehydrated(fn ($state) => filled($state))->visible(fn (string $context) => in_array($context, ['create', 'edit'])),
                Forms\Components\TextInput::make('status')->label('Status')->required()->maxLength(255)->default('active'),
            ]),
            Forms\Components\Fieldset::make('Document Information')->schema([
                Forms\Components\Select::make('document_type')->label('Document Type')->options([
                    'passport' => 'Passport',
                    'id_card' => 'ID Card',
                    'driver_license' => 'Driver License',
                    'residency_permit' => 'Residency Permit',
                    'other' => 'Other',
                ])->default('passport'),
                Forms\Components\TextInput::make('document_number')->label('Document Number')->maxLength(255),
                Forms\Components\FileUpload::make('document_photo')->label('Document Photo')->image()->directory('uploads/images')->maxSize(1024),
            ]),
            Forms\Components\Fieldset::make('Employment Information')->schema([
                Forms\Components\DatePicker::make('hired_date')->default(now())->label('Hired Date')->disabled(),
                Forms\Components\TextInput::make('hired_by')->default(\Illuminate\Support\Facades\Auth::user()?->name)->label('Hired By')->maxLength(255)->disabled(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('firstname')->label('First Name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('midname')->label('Middle Name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('lastname')->label('Last Name')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('phone')->label('Phone')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('address')->label('Address')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('birth_date')->label('Birth Date')->searchable()->sortable()->toggleable(),
                //Tables\Columns\ImageColumn::make('profile_photo')->label('Profile Photo')->circular()->size(40)->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('status')->label('Status')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('document_type')->label('Document Type')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('document_number')->label('Document Number')->searchable()->sortable()->toggleable(),
                //Tables\Columns\ImageColumn::make('document_photo')->label('Document Photo')->circular()->size(40)->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('nationality')->label('Nationality')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('hired_date')->label('Hired Date')->searchable()->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('hired_by')->label('Hired By')->searchable()->sortable()->toggleable(),
            ])
    ->filters([
        Tables\Filters\Filter::make('firstname'),
        Tables\Filters\Filter::make('midname'),
        Tables\Filters\Filter::make('lastname'),
        Tables\Filters\Filter::make('email'),
        Tables\Filters\Filter::make('phone'),
        Tables\Filters\Filter::make('address'),
        Tables\Filters\Filter::make('birth_date'),
        Tables\Filters\SelectFilter::make('status')->options([
            'active' => 'Active',
            'inactive' => 'Inactive',
        ]),
        Tables\Filters\SelectFilter::make('document_type')->options([
            'passport' => 'Passport',
            'id_card' => 'ID Card',
            'driver_license' => 'Driver License',
            'residency_permit' => 'Residency Permit',
            'other' => 'Other',
        ]),
    Tables\Filters\Filter::make('document_number'),
    Tables\Filters\Filter::make('nationality'),
    Tables\Filters\TernaryFilter::make('profile_photo')
        ->label('Has Profile Photo')
        ->trueLabel('With Photo')
        ->falseLabel('Without Photo')
        ->queries(
            true: fn (Builder $query) => $query->whereNotNull('profile_photo')->where('profile_photo', '!=', ''),
            false: fn (Builder $query) => $query->whereNull('profile_photo')->orWhere('profile_photo', ''),
        ),
    Tables\Filters\TernaryFilter::make('document_photo')
        ->label('Has Document Photo')
        ->trueLabel('With Photo')
        ->falseLabel('Without Photo')
        ->queries(
            true: fn (Builder $query) => $query->whereNotNull('document_photo')->where('document_photo', '!=', ''),
            false: fn (Builder $query) => $query->whereNull('document_photo')->orWhere('document_photo', ''),
        ),
        
    Tables\Filters\Filter::make('hired_date'),
    Tables\Filters\Filter::make('hired_by'),
])

            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export')
                    ->fileName('accounts-export')
                    ->defaultFormat('xlsx')
                    ->defaultPageOrientation('landscape')
                    ->disablePreview(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    FilamentExportBulkAction::make('export-selected')
                        ->label('Export Selected')
                        ->fileName('accounts-selected')
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
