<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'challenge_id',
        'question',
        'answer1',
        'answer2',
        'answer3',
        'correct_answer',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
    
