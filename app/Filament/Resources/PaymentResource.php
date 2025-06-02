<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Contract1;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
use Carbon\Carbon;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Widget;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    
    protected static ?string $navigationLabel = 'Payments';
    
    protected static ?string $modelLabel = 'Payment';
    
    protected static ?string $pluralModelLabel = 'Payments';
    
    protected static ?int $navigationSort = 5;
    
    protected static ?string $navigationGroup = 'Property Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Payment Information')
                    ->description('Enter payment details')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\Select::make('contract_id')
                                ->label('Contract')
                                ->relationship('contract', 'id')
                                ->getOptionLabelFromRecordUsing(function ($record) {
                                    $tenant = $record->tenant ? $record->tenant->firstname . ' ' . $record->tenant->lastname : 'No Tenant';
                                    $property = $record->property ? $record->property->name : 'No Property';
                                    $unit = $record->unit ? $record->unit->name : 'No Unit';
                                    return "#{$record->id} - {$tenant} | {$property} - {$unit}";
                                })
                                ->searchable(['id'])
                                ->preload()
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if ($state) {
                                        $contract = Contract1::with(['tenant', 'property', 'unit'])->find($state);
                                        if ($contract) {
                                            // Auto-fill tenant name and rental amount
                                            if ($contract->tenant) {
                                                $set('payer_name', $contract->tenant->firstname . ' ' . $contract->tenant->lastname);
                                            }
                                            if ($contract->unit) {
                                                $set('amount', $contract->unit->rental_price);
                                            }
                                        }
                                    }
                                })
                                ->columnSpan(2),
                        ]),
                        
                        Grid::make(3)->schema([
                            Forms\Components\TextInput::make('amount')
                                ->label('Payment Amount')
                                ->required()
                                ->numeric()
                                ->step(0.01)
                                ->suffix('JOD')
                                ->minValue(0)
                                ->maxValue(999999.99),
                                
                            Forms\Components\DatePicker::make('payment_date')
                                ->label('Payment Date')
                                ->required()
                                ->default(now())
                                ->maxDate(now()),
                                
                            Forms\Components\Select::make('payment_method')
                                ->label('Payment Method')
                                ->required()
                                ->options([
                                    'cash' => '💵 Cash',
                                    'bank_transfer' => '🏦 Bank Transfer',
                                    'wallet' => '📱 Digital Wallet',
                                    'cliq' => '⚡ CliQ',
                                ])
                                ->default('cash')
                                ->reactive(),
                        ]),
                    ])->columns(1),
                    
                Section::make('Payer & Receiver Information')
                    ->description('Details about who paid and who received')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('payer_name')
                                ->label('Payer Name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Who made the payment?'),
                                
                            Forms\Components\TextInput::make('receiver_name')
                                ->label('Receiver Name')
                                ->required()
                                ->maxLength(255)
                                ->default(fn () => Auth::user()?->name)
                                ->placeholder('Who received the payment?'),
                        ]),
                    ])->columns(1),
                    
                Section::make('Transfer Details')
                    ->description('Additional information for bank transfers and digital payments')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('bank_name')
                                ->label('Bank/Wallet Name')
                                ->maxLength(255)
                                ->placeholder('e.g., Arab Bank, Zain Cash, Orange Money')
                                ->visible(fn (callable $get) => in_array($get('payment_method'), ['bank_transfer', 'wallet', 'cliq'])),
                                
                            Forms\Components\TextInput::make('transfer_reference')
                                ->label('Transfer Reference')
                                ->maxLength(255)
                                ->placeholder('Transaction ID or Reference Number')
                                ->visible(fn (callable $get) => in_array($get('payment_method'), ['bank_transfer', 'wallet', 'cliq'])),
                        ]),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Additional Notes')
                            ->maxLength(1000)
                            ->rows(3)
                            ->placeholder('Any additional information about this payment...'),
                    ])->columns(1),
                    
                Section::make('Meta Information')
                    ->description('System information')
                    ->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('created_by')
                                ->label('Created By')
                                ->default(fn () => Auth::user()?->name)
                                ->disabled()
                                ->dehydrated(false),
                                
                            Forms\Components\DateTimePicker::make('created_at')
                                ->label('Created At')
                                ->default(now())
                                ->disabled()
                                ->dehydrated(false),
                        ]),
                    ])->columns(1)
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Payment ID')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract.id')
                    ->label('Contract')
                    ->sortable()
                    ->searchable()
                    ->formatStateUsing(fn ($record) => "#{$record->contract_id}")
                    ->badge()
                    ->color('info')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract.tenant.firstname')
                    ->label('Tenant')
                    ->searchable(['firstname', 'lastname'])
                    ->sortable()
                    ->formatStateUsing(function ($record) {
                        if ($record->contract && $record->contract->tenant) {
                            return $record->contract->tenant->firstname . ' ' . $record->contract->tenant->lastname;
                        }
                        return 'No Tenant';
                    })
                    ->copyable()
                    ->copyMessage('Tenant name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract.property.name')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Property name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract.unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('secondary')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('JOD')
                    ->sortable()
                    ->alignEnd()
                    ->copyable()
                    ->copyMessage('Amount copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'bank_transfer' => 'info',
                        'wallet' => 'warning', 
                        'cliq' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => '💵 Cash',
                        'bank_transfer' => '🏦 Bank Transfer',
                        'wallet' => '📱 Digital Wallet',
                        'cliq' => '⚡ CliQ',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('payer_name')
                    ->label('Payer')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Payer name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('receiver_name')
                    ->label('Receiver')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Receiver name copied!')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank/Wallet')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('transfer_reference')
                    ->label('Reference')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Reference copied!')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(50)
                    ->tooltip(function (Tables\Columns\TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= 50) {
                            return null;
                        }
                        return $state;
                    })
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('payment_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('contract_id')
                    ->label('Contract')
                    ->relationship('contract', 'id')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => '💵 Cash',
                        'bank_transfer' => '🏦 Bank Transfer',
                        'wallet' => '📱 Digital Wallet',
                        'cliq' => '⚡ CliQ',
                    ])
                    ->multiple(),
                    
                Tables\Filters\Filter::make('payment_date_range')
                    ->label('Payment Date Range')
                    ->form([
                        Forms\Components\DatePicker::make('from_date')
                            ->label('From Date'),
                        Forms\Components\DatePicker::make('to_date')
                            ->label('To Date'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from_date'], fn ($q, $date) => $q->where('payment_date', '>=', $date))
                            ->when($data['to_date'], fn ($q, $date) => $q->where('payment_date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from_date'] ?? null) {
                            $indicators['from_date'] = 'From: ' . Carbon::parse($data['from_date'])->format('Y-m-d');
                        }
                        if ($data['to_date'] ?? null) {
                            $indicators['to_date'] = 'To: ' . Carbon::parse($data['to_date'])->format('Y-m-d');
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('amount_range')
                    ->label('Amount Range')
                    ->form([
                        Forms\Components\TextInput::make('min_amount')
                            ->label('Minimum Amount')
                            ->numeric()
                            ->suffix('JOD'),
                        Forms\Components\TextInput::make('max_amount')
                            ->label('Maximum Amount')
                            ->numeric()
                            ->suffix('JOD'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min_amount'], fn ($q, $amount) => $q->where('amount', '>=', $amount))
                            ->when($data['max_amount'], fn ($q, $amount) => $q->where('amount', '<=', $amount));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min_amount'] ?? null) {
                            $indicators['min_amount'] = 'Min: ' . number_format($data['min_amount']) . ' JOD';
                        }
                        if ($data['max_amount'] ?? null) {
                            $indicators['max_amount'] = 'Max: ' . number_format($data['max_amount']) . ' JOD';
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('this_month')
                    ->label('This Month Payments')
                    ->query(function ($query) {
                        return $query->whereBetween('payment_date', [
                            now()->startOfMonth(),
                            now()->endOfMonth()
                        ]);
                    })
                    ->toggle(),
                    
                Tables\Filters\Filter::make('today')
                    ->label('Today\'s Payments')
                    ->query(function ($query) {
                        return $query->whereDate('payment_date', today());
                    })
                    ->toggle(),
                    
                Tables\Filters\Filter::make('has_reference')
                    ->label('Has Transfer Reference')
                    ->query(function ($query) {
                        return $query->whereNotNull('transfer_reference')
                                   ->where('transfer_reference', '!=', '');
                    })
                    ->toggle(),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export Payments')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('View')
                    ->color('info'),
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('Delete Selected')
                        ->color('danger'),
                    FilamentExportBulkAction::make('export-selected')
                        ->label('Export Selected')
                        ->color('success')
                        ->icon('heroicon-o-arrow-down-tray'),
                ]),
            ])
            ->emptyStateHeading('No Payments Found')
            ->emptyStateDescription('Start by creating a new payment record.')
            ->emptyStateIcon('heroicon-o-credit-card');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    
    public static function getNavigationBadgeColor(): string
    {
        return static::getModel()::count() > 100 ? 'warning' : 'success';
    }
    
    public static function getWidgets(): array
    {
        return [
            PaymentResource\Widgets\PaymentOverviewWidget::class,
        ];
    }
}