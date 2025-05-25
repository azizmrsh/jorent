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
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Grid;

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
        $livewire = $table->getLivewire();
        $isGrid = $livewire->isGridLayout();

        return $table
            ->columns(
                $isGrid ? static::getGridTableColumns() : static::getListTableColumns()
            )
            ->contentGrid(fn () => $isGrid ? ['md' => 1, 'lg' => 1, 'xl' => 3] : null)
            ->filters([])
            ->headerActions([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([])
            ->paginated(true)
            ->paginationPageOptions([9])
            ->defaultPaginationPageOption(9);
    }

    public static function getGridTableColumns(): array
    {
        return [
            Grid::make()
                ->schema([
                    Stack::make([
                        ImageColumn::make('profile_photo')
                            ->label('Photo')
                            ->square()
                            ->extraImgAttributes(['style' => 'object-fit: cover; border-radius: 10px; width: 60px; height: 60px']),

                        TextColumn::make('firstname')->weight('bold')->size('lg'),
                        TextColumn::make('email')->icon('heroicon-o-envelope')->color('gray'),
                        TextColumn::make('phone')->icon('heroicon-o-phone')->color('gray'),
                        TextColumn::make('nationality')->icon('heroicon-o-globe-alt')->color('gray'),

                        \App\Filament\Resources\AccResource\Pages\ListAccs::getSeparatorBlock(),

                        TextColumn::make('address')->icon('heroicon-o-map-pin')->color('gray'),

                        TextColumn::make('status')->badge()->color(fn ($state) => $state === 'active' ? 'success' : 'danger'),
                    ])
                ])
        ];
    }

    public static function getListTableColumns(): array
    {
        return [
            TextColumn::make('firstname')->searchable()->sortable(),
            TextColumn::make('email')->searchable()->sortable(),
            TextColumn::make('phone')->searchable()->sortable(),
            TextColumn::make('status')->badge()->sortable(),
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
