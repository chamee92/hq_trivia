<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'payment_type_id',
        'payment_id',
        'description',
        'status',
        'amount',
        'balance',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
    
