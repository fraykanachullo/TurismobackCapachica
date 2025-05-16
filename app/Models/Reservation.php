<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ← Falta esto



class Reservation extends Model
{

    use HasFactory; // ← Para permitir usar factories si las necesitas

    protected $fillable = [
        'user_id', 'service_id', 'reservation_date', 'people_count',
        'total_amount', 'status', 'paid_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }
}
