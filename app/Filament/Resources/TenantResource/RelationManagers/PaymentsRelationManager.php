<?php

namespace App\Filament\Resources\TenantResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $title = 'المدفوعات';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('contract_id')
                    ->relationship(
                        'contract', 
                        'id',
                        fn (Builder $query) => $query->where('tenant_id', $this->ownerRecord->id)
                    )
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $unitName = $record->unit->name ?? 'غير محدد';
                        return "عقد #{$record->id} - {$unitName}";
                    })
                    ->required()
                    ->label('العقد')
                    ->searchable()
                    ->preload(),
                    
                Forms\Components\TextInput::make('amount')
                    ->label('المبلغ')
                    ->numeric()
                    ->required()
                    ->prefix('دينار أردني')
                    ->step(0.01),
                    
                Forms\Components\DatePicker::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->required()
                    ->default(now()),
                    
                Forms\Components\Select::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options([
                        'cash' => 'نقداً',
                        'bank_transfer' => 'تحويل بنكي',
                        'wallet' => 'محفظة إلكترونية',
                        'cliq' => 'كليك',
                    ])
                    ->required()
                    ->default('cash'),
                    
                Forms\Components\TextInput::make('reference_number')
                    ->label('الرقم المرجعي')
                    ->maxLength(255)
                    ->placeholder('اختياري - رقم الإيصال أو المرجع'),
                    
                Forms\Components\Textarea::make('notes')
                    ->label('ملاحظات')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('رقم الدفعة')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('contract.id')
                    ->label('رقم العقد')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('contract.unit.name')
                    ->label('الوحدة')
                    ->sortable()
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('amount')
                    ->label('المبلغ')
                    ->money('JOD')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('payment_date')
                    ->label('تاريخ الدفع')
                    ->date()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('payment_method')
                    ->label('طريقة الدفع')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cash' => 'success',
                        'bank_transfer' => 'primary',
                        'wallet' => 'warning',
                        'cliq' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cash' => 'نقداً',
                        'bank_transfer' => 'تحويل بنكي',
                        'wallet' => 'محفظة إلكترونية',
                        'cliq' => 'كليك',
                        default => $state,
                    }),
                    
                Tables\Columns\TextColumn::make('reference_number')
                    ->label('الرقم المرجعي')
                    ->searchable()
                    ->limit(20)
                    ->placeholder('لا يوجد')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاريخ الإنشاء')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('payment_method')
                    ->label('طريقة الدفع')
                    ->options([
                        'cash' => 'نقداً',
                        'bank_transfer' => 'تحويل بنكي',
                        'wallet' => 'محفظة إلكترونية',
                        'cliq' => 'كليك',
                    ]),
                    
                Tables\Filters\Filter::make('payment_date_range')
                    ->label('نطاق تاريخ الدفع')
                    ->form([
                        Forms\Components\DatePicker::make('payment_from')
                            ->label('من تاريخ'),
                        Forms\Components\DatePicker::make('payment_until')
                            ->label('إلى تاريخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['payment_from'], fn ($query, $date) => $query->whereDate('payment_date', '>=', $date))
                            ->when($data['payment_until'], fn ($query, $date) => $query->whereDate('payment_date', '<=', $date));
                    }),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('إضافة دفعة جديدة'),
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
            ])
            ->defaultSort('payment_date', 'desc');
    }
} 