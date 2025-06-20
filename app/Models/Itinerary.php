<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Itinerary extends Model
{
    use HasFactory;

    protected $fillable = [
        'itineraryable_type',
        'itineraryable_id',
        'day_number',
        'start_time',
        'end_time',
        'title',
        'description',
    ];

    /**
     * Polimórfica: cada itinerario pertenece a un Service, Package, Promotion, etc.
     */
    public function itineraryable(): MorphTo
    {
        return $this->morphTo();
    }
}
