<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'contract_id', // Optional, as custom invoices might not always be tied to a contract
        'invoice_number',
        'issue_date',
        'due_date',
        'amount',
        'status',
        'payment_date',
        'description', // Specific to custom invoices
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contract(): BelongsTo
    {
        // Custom invoices might not always have a contract
        return $this->belongsTo(Contract::class)->nullable();
    }
}