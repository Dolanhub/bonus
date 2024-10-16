<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashBack extends Model
{


    // use HasFactory;
    protected $table='cash_backs';

    protected $fillable = [
        'idupload',
        'user_id',
        'member',
        'total',
        'status',
        'responseapi',
        'create_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
