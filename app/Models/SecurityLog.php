<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;  // IMPORTANTE agregar

class SecurityLog extends Model
{
    protected $fillable = [
        'user_id',
        'event',
        'ip_address',
        'user_agent',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
