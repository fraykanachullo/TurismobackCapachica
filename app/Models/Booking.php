<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reservation_date',
        'total_amount',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'reservation_date' => 'date',
        'paid_at'          => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
