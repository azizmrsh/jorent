<?php

namespace App\Filament\Resources;

use App\Filament\Resources\Contract1Resource\Pages;
use App\Models\Contract1;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Unit;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;

// Export functionality imports
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use AlperenErsoy\FilamentExport\Actions\FilamentExportHeaderAction;



class Contract1Resource extends Resource
{
    protected static ?string $model = Contract1::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Rental Management';
    protected static ?string $navigationLabel = 'Contracts';

    public static function form(Form $form): Form
    {
        return $form->schema([

            /// Landlord and Tenant Info
            Forms\Components\Section::make('Landlord and Tenant Information')->schema([
                Forms\Components\TextInput::make('landlord_name')
                    ->label('Landlord Name')
                    ->required(),

              Forms\Components\Select::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->required(),  
            ])->columns(2),

///////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Property & Unit        

            Forms\Components\Section::make('Property & Unit Details')->schema([
                Forms\Components\Select::make('property_id')
                    ->options(Property::all()->pluck('name', 'id'))
                    ->required()
                    ->label('Property')
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $property = Property::with('address')->find($state);
                        if ($property?->address) {
                            $set('governorate', $property->address->governorate);
                            $set('city', $property->address->city);
                            $set('district', $property->address->district);
                            $set('building_number', $property->address->building_number);
                            $set('plot_number', $property->address->plot_number);
                            $set('basin_number', $property->address->basin_number);
                            $set('property_number', $property->address->property_number);
                            $set('street_name', $property->address->street_name);
                        }
                        $set('unit_id', null);
                    }),
                Forms\Components\Select::make('unit_id')
                    ->options(fn (callable $get) =>
                        Unit::where('property_id', $get('property_id'))->pluck('name', 'id')
                    )
                    ->required()
                    ->label('Unit'),

                Forms\Components\TextInput::make('governorate')->label('Governorate')->readOnly(),
                Forms\Components\TextInput::make('city')->label('City')->readOnly(),
                Forms\Components\TextInput::make('district')->label('District')->readOnly(),
                Forms\Components\TextInput::make('building_number')->label('Building Number')->readOnly(),
                Forms\Components\TextInput::make('plot_number')->label('Plot Number')->readOnly(),
                Forms\Components\TextInput::make('basin_number')->label('Basin Number')->readOnly(),
                Forms\Components\TextInput::make('property_number')->label('Property Number')->readOnly(),
                Forms\Components\TextInput::make('street_name')->label('Street Name')->readOnly(),
            ])->columns(3),
//////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Contract Details
            Forms\Components\Section::make('Contract Details')->schema([
                Forms\Components\DatePicker::make('start_date')
                    ->label('Start Date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->label('End Date')
                    ->required(),
                Forms\Components\DatePicker::make('due_date')
                    ->label('Due Date'),
                Forms\Components\TextInput::make('rent_amount')
                    ->label('Rent Amount')
                    ->numeric()
                    ->required(),
                Forms\Components\Select::make('status')
                    ->label('Contract Status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->reactive()
                    ->afterStateHydrated(function (callable $set, callable $get) {
                        $startDate = $get('start_date');
                        $endDate = $get('end_date');

                        if ($startDate && $endDate && now()->between($startDate, $endDate)) {
                            $set('status', 'active');
                        } else {
                            $set('status', 'inactive');
                        }
                    }),
            ])->columns(3),

 Forms\Components\Section::make('Terms and Conditions')->schema([
                Forms\Components\Textarea::make('terms_and_conditions_extra')
                    ->label('Additional Terms and Conditions'),
                Forms\Components\Actions::make([
                    Forms\Components\Actions\Action::make('show_terms')
                        ->label('View Default Terms')
                        ->color('primary')
                        ->modalHeading('Default Terms and Conditions')
                        ->modalContent(fn() => new \Illuminate\Support\HtmlString('<div style="text-align:left;direction:ltr;font-size:15px;white-space:pre-line;line-height:2;">
<strong>First:</strong> The preamble of this contract, its terms and attachments, if any, are considered an integral part of it and are read together as one unit.
<strong>Second:</strong> The tenant acknowledges that he has received the leased property and its accessories free from any defect and has personally inspected all doors, windows, glass, and locks with their keys, sinks, faucets, sanitary equipment, paint, tiles, ceramics, plaster, and all decorations, and that all these items and accessories are new, sound, and free from any defect or malfunction. The tenant undertakes to deliver them upon the expiration of the lease term in new condition as received.
<strong>Third:</strong> The tenant must, before the expiration of the contract period, if he does not wish to renew for a similar period, notify the landlord at least two months before the expiration of the contract period, otherwise he is considered a tenant of the property for another similar period if the landlord so desires, with emphasis that this condition does not apply to the landlord.
<strong>Fourth:</strong> The tenant may not lease the leased property or part of it to others or bring in a partner or company with him in the leased property or assign it wholly or partially to others without the written consent of the landlord.
<strong>Fifth:</strong> The tenant has no right to make any changes to the leased property such as demolition, construction, opening windows, creating mezzanines, or making any changes to doors, faucets, or drilling walls, etc., except with the written consent of the landlord. In all cases, he must restore them at his own expense to the condition in which he received them when signing the contract. The tenant must return any accessories he received with the leased property in the condition he received them.
<strong>Sixth:</strong> Any malfunction, defect, damage, or deterioration that occurs in the leased property in the sewers, sanitary or electrical installations, plastering, finishes, or any of the facilities attached to the leased property shall be repaired by the tenant, and he has no right to demand compensation from the landlord. He also has no right to demand any compensation or damages from the landlord, whatever their nature, due to any disruption or malfunction that occurs in the shared services attached to the building.
<strong>Seventh:</strong> The tenant is obligated to pay all fees, expenses, costs, and bills imposed on the leased property, including security and cleaning fees, electricity, telephone, roofing tax, and education tax, in addition to all maintenance expenses and others.
<strong>Eighth:</strong> If the tenant refuses or delays payment of any installment of the rent after ten days from its due date, all contract installments become immediately due and payable in one payment. The landlord also has the right and option to terminate this contract and take possession of the leased property even if the lease period has not ended. He also has the right to take possession of it and lease it to others at the rent he deems appropriate, provided that the difference between the two rents is borne by the tenant if the second rent is less than the first.
<strong>Ninth:</strong> In case of occurrence of either of the two matters mentioned in the previous two clauses of this contract, the landlord also has the right to take possession of the tenant\'s property located in the leased property and sell it at the price he deems appropriate and collect his rights from its price.
<strong>Tenth:</strong> The landlord has the right to build upper floors above the leased property or near it and to carry out all repairs and renovations he wants in the leased property and its accessories or nearby, regardless of the time required during this lease period or the period to which it extends. The tenant may not demand compensation from the landlord for any malfunction, damage, or reduction in rent due to these works.
<strong>Eleventh:</strong> All improvements, repairs, decorations, or other work carried out by the tenant shall be at his expense alone, and upon his departure, the landlord has the option of either taking them as they are without compensation or requesting the restoration of the leased property as it was at the moment of this contract. In that case, the expenses of restoration and removal, no matter how much, shall be borne by the tenant alone.
<strong>Twelfth:</strong> The tenant may not use the leased property for purposes other than those for which it was leased or use it in a manner that violates Sharia, law, public order, and public morals. He may not create noise or cause disturbance to neighbors.
<strong>Thirteenth:</strong> If the tenants in this contract are more than one person, they are considered jointly and severally liable for all obligations arising from it. If the tenant is a company or legal entity, the person or persons who sign on behalf of the company or institution (legal entity) are considered jointly and severally responsible with it for all tenant responsibilities in this contract and its resulting obligations throughout the duration of this contract and any other periods to which it is renewed.
<strong>Fourteenth:</strong> If the landlord wishes to terminate the contract at the end of its term or does not wish to renew the contract for a similar period, he is exempt from issuing the warning required by the Landlord and Tenant Law, and an urgent application may be submitted to terminate the expiration of the period required by law.
<strong>Fifteenth:</strong> The tenant may not violate building and planning provisions and is obligated to bear any violation or fine resulting from violation of laws, regulations, municipal instructions, or provisions of the Floors and Apartments Law or Amman Municipality throughout the period of his occupation of the property.
<strong>Sixteenth:</strong> The tenant is obligated at the end of the contract period to bring a clearance certificate to the landlord from the electricity company, water authority, and municipality proving that there are no amounts due on the leased property during the rental period.
<strong>Seventeenth:</strong> The landlord has the right to determine the locations for placing satellite dishes, electronic and radio receivers, and water tanks. The tenant has no right to add additional water tanks without the written consent of the landlord, regardless of the purpose of the lease.
<strong>Eighteenth:</strong> If the leased property is an apartment, the tenant is obligated to comply with the provisions of the Real Estate Ownership Law and the Apartment Management System and is obligated to pay the dues imposed on the apartment for the management or use of shared services. If the building has a guard or cleaner, he is obligated to pay his dues and is obligated to pay any expenses for maintaining shared services, including elevator maintenance or roof maintenance even if he does not use them. He is obligated to pay his share of water and electricity bills due on shared services and may not under any circumstances refuse to participate in shared service expenses. He may not claim non-benefit from them and must adhere to the designated place for parking his car and may not encroach on parking spaces designated for other residents.
<strong>Nineteenth:</strong> Failure to respect the neighbors living in the building where the apartment is located or those facing them or harming any of the neighbors with any actions not accepted by custom and tradition is considered a reason for contract termination, and the tenant is obligated to compensate for any malfunction or damage that affects the owner or others.
</div>'))
                        ->modalSubmitAction(false)
                ]),
     ])->columns(3),

//////////////////////////////////////////////////////////////////////////////////////////////////////////////
            /// Signatures
Forms\Components\Section::make('Digital Signatures')
    ->schema([
    SignaturePad::make('tenant_signature')
    ->label('Tenant Signature')
    ->required()
    ->dehydrateStateUsing(function ($state, callable $set) {
        if ($state) {
            // Remove base64 prefix
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
            
            // Generate random filename
            $fileName = 'signatures/' . Str::uuid() . '.png';

            // Save file to storage/app/public/signatures
            Storage::disk('public')->put($fileName, $imageData);

            // Save only the path
            $set('tenant_signature_path', $fileName);
        }

        return null; // Don't store base64
    }),
    // Landlord signature
        SignaturePad::make('landlord_signature')
            ->label('Landlord Signature')
            ->required()
            ->dehydrateStateUsing(function ($state, callable $set) {
                if ($state) {
                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
                    $fileName = 'signatures/' . Str::uuid() . '.png';
                    Storage::disk('public')->put($fileName, $imageData);
                    $set('landlord_signature_path', $fileName);
                }
                return null;
            }),

//        // First Witness Signature
//        SignaturePad::make('witness1_signature')
//            ->label('First Witness Signature')
//            ->required()
//            ->dehydrateStateUsing(function ($state, callable $set) {
//                if ($state) {
//                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
//                    $fileName = 'signatures/' . Str::uuid() . '.png';
//                    Storage::disk('public')->put($fileName, $imageData);
//                    $set('witness1_signature_path', $fileName);
//                }
//                return null;
//            }),
//
//        // Second Witness Signature
//        SignaturePad::make('witness2_signature')
//            ->label('Second Witness Signature')
//            ->required()
//            ->dehydrateStateUsing(function ($state, callable $set) {
//                if ($state) {
//                    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $state));
//                    $fileName = 'signatures/' . Str::uuid() . '.png';
//                    Storage::disk('public')->put($fileName, $imageData);
//                    $set('witness2_signature_path', $fileName);
//                }
//                return null;
//            }),
    ])->columns(4),
            



        // Pen color on export (defaults to penColor)
            Forms\Components\Section::make('Meta Information')->schema([
                Forms\Components\DatePicker::make('hired_date')
                    ->label('Created Date')
                    ->default(now())
                    ->readOnly(),
                Forms\Components\TextInput::make('hired_by')
                    ->label('Created By')
                    ->default(fn () => Auth::user()?->name)
                    ->readOnly(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Contract ID')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('landlord_name')
                    ->label('Landlord')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Landlord name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.firstname')
                    ->label('Tenant')
                    ->searchable(['firstname', 'lastname'])
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->tenant ? $record->tenant->firstname . ' ' . $record->tenant->lastname : 'Not specified')
                    ->copyable()
                    ->copyMessage('Tenant name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('tenant.phone')
                    ->label('Tenant Phone')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Phone number copied!')
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('property.name')
                    ->label('Property')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Property name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->copyMessage('Unit name copied!')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('unit.rental_price')
                    ->label('Rental Price')
                    ->sortable()
                    ->money('JOD')
                    ->alignEnd()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Start Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('end_date')
                    ->label('End Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('contract_duration')
                    ->label('Contract Duration')
                    ->getStateUsing(function ($record) {
                        if ($record->start_date && $record->end_date) {
                            $start = \Carbon\Carbon::parse($record->start_date);
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $months = $start->diffInMonths($end);
                            return $months . ' months';
                        }
                        return 'Not specified';
                    })
                    ->badge()
                    ->color('secondary')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'inactive' => 'danger',
                        'expired' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                        default => $state,
                    })
                    ->sortable()
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('days_remaining')
                    ->label('Days Remaining')
                    ->getStateUsing(function ($record) {
                        if ($record->end_date) {
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $now = \Carbon\Carbon::now();
                            if ($end->isFuture()) {
                                return $now->diffInDays($end) . ' days';
                            }
                            return 'Expired';
                        }
                        return 'Not specified';
                    })
                    ->badge()
                    ->color(function ($record) {
                        if ($record->end_date) {
                            $end = \Carbon\Carbon::parse($record->end_date);
                            $now = \Carbon\Carbon::now();
                            if ($end->isFuture()) {
                                $days = $now->diffInDays($end);
                                if ($days <= 30) return 'danger';
                                if ($days <= 90) return 'warning';
                                return 'success';
                            }
                        }
                        return 'gray';
                    })
                    ->toggleable(),
                    
                Tables\Columns\IconColumn::make('has_signatures')
                    ->label('Signatures')
                    ->boolean()
                    ->getStateUsing(fn ($record) => !empty($record->tenant_signature_path) && !empty($record->landlord_signature_path))
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),
                    
                Tables\Columns\TextColumn::make('hired_by')
                    ->label('Created By')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('hired_date')
                    ->label('Created Date')
                    ->date('Y-m-d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date Added')
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('property_id')
                    ->label('Property')
                    ->relationship('property', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->relationship('unit', 'name')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'firstname')
                    ->searchable()
                    ->preload(),
                    
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'expired' => 'Expired',
                    ])
                    ->multiple(),
                    
                Tables\Filters\Filter::make('contract_dates')
                    ->label('Contract Dates')
                    ->form([
                        Forms\Components\DatePicker::make('start_date_from')
                            ->label('Start Date From'),
                        Forms\Components\DatePicker::make('start_date_until')
                            ->label('Start Date Until'),
                        Forms\Components\DatePicker::make('end_date_from')
                            ->label('End Date From'),
                        Forms\Components\DatePicker::make('end_date_until')
                            ->label('End Date Until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['start_date_from'], fn ($q, $date) => $q->where('start_date', '>=', $date))
                            ->when($data['start_date_until'], fn ($q, $date) => $q->where('start_date', '<=', $date))
                            ->when($data['end_date_from'], fn ($q, $date) => $q->where('end_date', '>=', $date))
                            ->when($data['end_date_until'], fn ($q, $date) => $q->where('end_date', '<=', $date));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['start_date_from'] ?? null) {
                            $indicators['start_date_from'] = 'Start Date From: ' . \Carbon\Carbon::parse($data['start_date_from'])->format('Y-m-d');
                        }
                        if ($data['start_date_until'] ?? null) {
                            $indicators['start_date_until'] = 'Start Date Until: ' . \Carbon\Carbon::parse($data['start_date_until'])->format('Y-m-d');
                        }
                        if ($data['end_date_from'] ?? null) {
                            $indicators['end_date_from'] = 'End Date From: ' . \Carbon\Carbon::parse($data['end_date_from'])->format('Y-m-d');
                        }
                        if ($data['end_date_until'] ?? null) {
                            $indicators['end_date_until'] = 'End Date Until: ' . \Carbon\Carbon::parse($data['end_date_until'])->format('Y-m-d');
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('rental_price_range')
                    ->label('Rental Price Range')
                    ->form([
                        Forms\Components\TextInput::make('min_price')
                            ->label('Minimum Price')
                            ->numeric()
                            ->suffix('JOD'),
                        Forms\Components\TextInput::make('max_price')
                            ->label('Maximum Price')
                            ->numeric()
                            ->suffix('JOD'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['min_price'], function ($q, $price) {
                                return $q->whereHas('unit', fn ($query) => $query->where('rental_price', '>=', $price));
                            })
                            ->when($data['max_price'], function ($q, $price) {
                                return $q->whereHas('unit', fn ($query) => $query->where('rental_price', '<=', $price));
                            });
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['min_price'] ?? null) {
                            $indicators['min_price'] = 'Minimum Price: ' . number_format($data['min_price']) . ' JOD';
                        }
                        if ($data['max_price'] ?? null) {
                            $indicators['max_price'] = 'Maximum Price: ' . number_format($data['max_price']) . ' JOD';
                        }
                        return $indicators;
                    }),
                    
                Tables\Filters\Filter::make('expiring_soon')
                    ->label('Contracts Expiring Soon')
                    ->query(function ($query) {
                        return $query->where('end_date', '>=', now())
                                    ->where('end_date', '<=', now()->addDays(30));
                    })
                    ->toggle(),
                    
                Tables\Filters\Filter::make('with_signatures')
                    ->label('Contracts with Signatures')
                    ->query(function ($query) {
                        return $query->whereNotNull('tenant_signature_path')
                                    ->whereNotNull('landlord_signature_path');
                    })
                    ->toggle(),
                    
                Tables\Filters\Filter::make('created_this_month')
                    ->label('Created This Month')
                    ->query(function ($query) {
                        return $query->whereBetween('created_at', [
                            now()->startOfMonth(),
                            now()->endOfMonth()
                        ]);
                    })
                    ->toggle(),
            ])
            ->headerActions([
                FilamentExportHeaderAction::make('export')
                    ->label('Export Contracts')
                    ->color('success')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->fileName('contracts_' . date('Y-m-d'))
                    ->withColumns([
                        'id' => 'Contract ID',
                        'landlord_name' => 'Landlord',
                        'tenant_name' => 'Tenant Name',
                        'tenant_phone' => 'Tenant Phone',
                        'tenant_email' => 'Tenant Email',
                        'property_name' => 'Property',
                        'unit_name' => 'Unit',
                        'rental_price' => 'Rental Price',
                        'start_date' => 'Start Date',
                        'end_date' => 'End Date',
                        'status' => 'Status',
                        'hired_by' => 'Created By',
                        'hired_date' => 'Creation Date',
                        'created_at' => 'Added Date',
                    ]),
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
                        ->icon('heroicon-o-arrow-down-tray')
                        ->fileName('selected_contracts_' . date('Y-m-d'))
                        ->withColumns([
                            'id' => 'Contract ID',
                            'landlord_name' => 'Landlord',
                            'tenant_name' => 'Tenant Name',
                            'tenant_phone' => 'Tenant Phone',
                            'property_name' => 'Property',
                            'unit_name' => 'Unit',
                            'rental_price' => 'Rental Price',
                            'start_date' => 'Start Date',
                            'end_date' => 'End Date',
                            'status' => 'Status',
                            'created_at' => 'Added Date',
                        ]),
                ]),
            ])
            ->emptyStateHeading('No Contracts Found')
            ->emptyStateDescription('Start by creating a new contract.')
            ->emptyStateIcon('heroicon-o-document-text');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContract1s::route('/'),
            'create' => Pages\CreateContract1::route('/create'),
            'edit' => Pages\EditContract1::route('/{record}/edit'),
           //'view' => Pages\ViewContract1::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function canView($record): bool
    {
        return true;
    }
}
