<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserChallenge extends Model
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
        'has_watched',
        'has_like',
        'has_attend_quiz',
        'correct_answer_count',
        'wrong_answer_count',
        'earn_amount',
        'earn_coin',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
    
