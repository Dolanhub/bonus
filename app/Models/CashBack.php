<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBack extends Model
{
    // use HasFactory;
    protected $table='cash_backs';
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
