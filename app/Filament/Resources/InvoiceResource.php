<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Financial';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Invoices'; // Corrected pluralization
    protected static ?string $label = 'Invoice';
    protected static ?string $pluralLabel = 'Invoices';
    protected static ?string $slug = 'invoices'; // Conventionally lowercase and plural
    protected static ?string $recordTitleAttribute = 'invoice_number'; // Or a more descriptive attribute

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('contract_id')
                    ->relationship('contract', 'id') // Assuming 'id' or a descriptive field on Contract
                    ->required()
                    ->label('Contract')
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, $state) {
                        // Optionally, auto-fill tenant based on contract
                        // $contract = \App\Models\Contract::find($state);
                        // if ($contract) {
                        //    $set('tenant_id', $contract->tenant_id);
                        // }
                    }),
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'firstname') // Assuming 'firstname' on Tenant
                    ->required()
                    ->label('Tenant')
                    ->searchable()
                    ->preload(),
                    // ->disabled(fn (callable $get) => !$get('contract_id')), // Disable if tenant is auto-filled
                Forms\Components\TextInput::make('invoice_number')
                    ->required()
                    ->maxLength(255)
                    ->label('Invoice Number')
                    ->default(fn () => 'INV-' . strtoupper(uniqid())),
                Forms\Components\DatePicker::make('issue_date')
                    ->required()
                    ->label('Issue Date')
                    ->default(now()),
                Forms\Components\DatePicker::make('due_date')
                    ->required()
                    ->label('Due Date')
                    ->after('issue_date'),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('JOD') // Or your currency
                    ->label('Amount'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ])
                    ->required()
                    ->default('pending')
                    ->label('Status'),
                Forms\Components\DatePicker::make('payment_date')
                    ->label('Payment Date')
                    ->nullable(),
                Forms\Components\Textarea::make('notes')
                    ->label('Notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable()
                    ->label('Invoice #'),
                Tables\Columns\TextColumn::make('contract.id') // Or a more descriptive field from Contract
                    ->label('Contract ID')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tenant.firstname')
                    ->label('Tenant')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->money('JOD') // Or your currency
                    ->sortable()
                    ->label('Amount'),
                Tables\Columns\TextColumn::make('issue_date')
                    ->date()
                    ->sortable()
                    ->label('Issue Date'),
                Tables\Columns\TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->label('Due Date'),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending' => 'warning',
                        'overdue' => 'danger',
                        'cancelled' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('payment_date')
                    ->date()
                    ->sortable()
                    ->label('Payment Date')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->relationship('tenant', 'firstname')
                    ->label('Tenant'),
                Tables\Filters\SelectFilter::make('contract_id')
                    ->relationship('contract', 'id') // Or a more descriptive field
                    ->label('Contract'),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        'cancelled' => 'Cancelled',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
}
