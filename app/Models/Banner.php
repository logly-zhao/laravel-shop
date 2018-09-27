<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'is_front', 'image'
    ];
    protected $casts = [
        'is_front' => 'boolean',
    ];

}
