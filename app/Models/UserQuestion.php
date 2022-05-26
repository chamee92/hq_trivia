<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuestion extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'challenge_id',
        'question_id',
        'your_answer',
        'earn_amount',
        'earn_coin',
        'is_correct',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
    
