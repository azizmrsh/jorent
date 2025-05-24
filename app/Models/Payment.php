<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'amount',
        'payment_date',
        'status',
        'contract_id',
    ];


    public function contract()
{
    return $this->belongsTo(Contract::class);
}
   
}
