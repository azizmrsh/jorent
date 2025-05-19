<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'tenant_id',
        'invoice_number',
        'issue_date',
        'due_date',
        'amount',
        'status',
        'payment_date',
        'notes',
        // Add 'description' if you have custom invoices that might not be linked to a contract
        // and need a separate description field in the Invoice model itself.
        // If 'description' is only for CustomInvoice model, then it's not needed here.
        // 'description', 
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    // If you have a separate CustomInvoice model and it has a relationship to Invoice, define it here.
    // Or, if Invoice can be a "custom invoice" itself, you might not need a separate model.
    // For now, assuming 'description' was for CustomInvoicesRelationManager and not a direct field on the main Invoice model.
}