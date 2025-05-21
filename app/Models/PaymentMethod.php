<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_number',
        'cardholder_name',
        'expiry_month',
        'expiry_year',
        'cvv',
        'brand',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
