<?php

// ✅ Grid view now includes popup confirmation and toast notifications on actions

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
use Hydrat\TableLayoutToggle\TableLayoutTogglePlugin;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Actions\Action;

use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Notifications\Notification;

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
            Forms\Components\TextInput::make('firstname')->label('First Name')->required()->maxLength(255),
            Forms\Components\TextInput::make('midname')->label('Middle Name')->maxLength(255),
            Forms\Components\TextInput::make('lastname')->label('Last Name')->maxLength(255),
            Forms\Components\TextInput::make('email')->label('Email')->email(),
            Forms\Components\TextInput::make('phone')->label('Phone'),
            Forms\Components\TextInput::make('address')->label('Address'),
            Forms\Components\TextInput::make('nationality')->label('Nationality'),
            Forms\Components\TextInput::make('status')->label('Status'),
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
            ->contentGrid(
                fn () => $isGrid ? ['md' => 1, 'lg' => 1, 'xl' => 3] : null
            )
            ->filters($isGrid ? [] : [
                Tables\Filters\Filter::make('firstname'),
                Tables\Filters\Filter::make('email'),
            ])
 //           ->headerActions([
 //               \Hydrat\TableLayoutToggle\Actions\ToggleTableLayoutAction::make(),
 //           ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
                    ImageColumn::make('profile_photo')
                        ->label('')
                        ->square()
                        ->size(60),

                    Stack::make([
                        TextColumn::make('firstname')
                            ->weight('bold')
                            ->size('lg'),
                        TextColumn::make('email')
                            ->size('sm')
                            ->color('gray'),
                        TextColumn::make('phone')
                            ->size('sm')
                            ->color('gray'),
                        TextColumn::make('nationality')
                            ->size('sm')
                            ->color('gray'),

                        TextColumn::make('address')
                            ->icon('heroicon-o-map-pin')
                            ->color('gray')
                            ->size('sm')
                            ->separatorAbove(),

                        TextColumn::make('status')
                            ->badge()
                            ->color(fn ($state) => $state === 'active' ? 'success' : 'danger'),

                        Stack::make([
                            Action::make('Send Email')
                                ->label('✉️ Send Email')
                                ->icon('heroicon-o-envelope')
                                ->color('primary')
                                ->action(fn ($record) => Notification::make()
                                    ->title('Opening Email')
                                    ->body('Redirecting to email client...')
                                    ->success()
                                    ->send())
                                ->url(fn ($record) => 'mailto:' . $record->email, true),

                            Action::make('WhatsApp')
                                ->label('💬 WhatsApp')
                                ->icon('heroicon-o-chat-bubble-left-right')
                                ->color('success')
                                ->requiresConfirmation()
                                ->modalHeading('Send WhatsApp Message?')
                                ->modalDescription('You will be redirected to WhatsApp Web or App.')
                                ->action(fn ($record) => Notification::make()
                                    ->title('Opening WhatsApp')
                                    ->body('Redirecting to WhatsApp...')
                                    ->success()
                                    ->send())
                                ->url(fn ($record) => 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->phone), true),
                        ])->separatorAbove(),
                    ])
                ])
                ->extraAttributes(['class' => 'bg-white p-6 rounded-2xl shadow-md border border-gray-200 hover:shadow-lg transition-all relative'])
        ];
    }

    public static function getListTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('firstname')->label('First Name')->searchable()->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('midname')->label('Middle Name')->searchable()->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('lastname')->label('Last Name')->searchable()->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('phone')->label('Phone')->searchable()->sortable()->toggleable(),
            Tables\Columns\ImageColumn::make('profile_photo')->label('Profile Photo')->circular()->size(40)->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('status')->label('Status')->searchable()->sortable()->toggleable(),
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
