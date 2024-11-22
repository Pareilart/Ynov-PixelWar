<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pixel extends Model
{
    protected $fillable = [
        'color',
        'x',
        'y',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
