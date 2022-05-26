<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'challenge_name',
        'challenge_description',
        'image_path',
        'video_path',
        'start_date_time',
        'end_date_time',
        'video_duration',
        'quiz_duration',
        'question_duration',
        'question_start_time',
        'question_end_time',
        'total_price',
        'total_coin',
        'question_count',
        'question_price',
        'question_coin',
        'total_watch',
        'total_like',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
    
