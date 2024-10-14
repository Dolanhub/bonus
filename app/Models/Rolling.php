<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rolling extends Model
{
    // use HasFactory;
    protected $table='rollings';

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
