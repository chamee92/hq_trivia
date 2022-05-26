<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'invoice_number',
        'description',
        'amount',
        'paid_amount',
        'coin_amount',
        'paid_coin_amount',
        'status',
        'transaction_data',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
    
