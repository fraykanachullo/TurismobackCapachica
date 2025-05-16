<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'reservation_id', 'method', 'transaction_id',
        'amount', 'currency', 'status', 'paid_at'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
