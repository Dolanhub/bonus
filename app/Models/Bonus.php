<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    // use HasFactory;
    protected $table='bonuses';
    protected $fillable = [
        'idupload',
        'user_id',
        'member',
        'totaldepo',
        'bonus',
        'status',
        'responseapi',
        'create_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
