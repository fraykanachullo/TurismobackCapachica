<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBehavior extends Model
{
    protected $fillable = [
        'user_id', 'last_seen_service_id', 'preferred_categories',
        'viewed_services', 'clicked_services', 'reserved_services'
    ];

    protected $casts = [
        'preferred_categories' => 'array',
        'viewed_services' => 'array',
        'clicked_services' => 'array',
        'reserved_services' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
