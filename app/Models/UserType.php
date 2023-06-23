<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_type',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
    
