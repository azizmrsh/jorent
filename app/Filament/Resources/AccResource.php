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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
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
            Forms\Components\TextInput::make('firstname')->required(),
            Forms\Components\TextInput::make('midname'),
            Forms\Components\TextInput::make('lastname'),
            Forms\Components\TextInput::make('email'),
            Forms\Components\TextInput::make('phone'),
            Forms\Components\TextInput::make('address'),
            Forms\Components\TextInput::make('nationality'),
            Forms\Components\TextInput::make('status'),
            Forms\Components\FileUpload::make('profile_photo')->image(),
        ]);
    }

    public static function table(Table $table): Table
    {
        $isGrid = request()->query('layout') === 'grid';

        return $table
            ->columns($isGrid ? static::getGridTableColumns() : static::getListTableColumns())
            ->contentGrid(fn () => $isGrid ? ['md' => 2, 'lg' => 3, 'xl' => 3] : null)
            ->filters([])
            ->headerActions([
                Action::make('toggleLayout')
                    ->label($isGrid ? 'عرض كجدول' : 'عرض كبطاقات')
                    ->icon('heroicon-o-view-columns')
                    ->url(fn () => request()->fullUrlWithQuery([
                        'layout' => $isGrid ? 'list' : 'grid',
                    ]))
                    ->color('gray'),
                FilamentExportHeaderAction::make('export')
                    ->label('Export')
                    ->fileName('accounts-export')
                    ->defaultFormat('xlsx')
                    ->defaultPageOrientation('landscape')
                    ->disablePreview(),
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
                DeleteAction::make(),
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

    public static function getGridTableColumns(): array
    {
        return [
            Grid::make()
                ->schema([
                    ImageColumn::make('profile_photo')->label('')->size(60)->circular(),
                    Stack::make([
                        TextColumn::make('firstname')->weight('bold'),
                        TextColumn::make('email')->size('sm')->color('gray'),
                        TextColumn::make('phone')->size('sm')->color('gray'),
                    ]),
                ])
                ->extraAttributes(['class' => 'bg-white p-4 rounded-lg shadow border'])
        ];
    }

    public static function getListTableColumns(): array
    {
        return [
            TextColumn::make('firstname')->label('First Name')->searchable()->sortable()->toggleable(),
            TextColumn::make('midname')->label('Middle Name')->searchable()->sortable()->toggleable(),
            TextColumn::make('lastname')->label('Last Name')->searchable()->sortable()->toggleable(),
            TextColumn::make('email')->label('Email')->searchable()->sortable()->toggleable(),
            TextColumn::make('phone')->label('Phone')->searchable()->sortable()->toggleable(),
            ImageColumn::make('profile_photo')->label('Profile Photo')->circular()->size(40)->sortable()->toggleable(),
            TextColumn::make('status')->label('Status')->searchable()->sortable()->toggleable(),
        ];
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
