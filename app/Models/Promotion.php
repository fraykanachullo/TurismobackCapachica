<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'title',
        'description',
        'discount_percentage',
        'start_date',
        'end_date',
        'status',
    ];

    // Una promoción pertenece a una empresa
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Una promoción puede aplicarse a muchos servicios (pivot con timestamps)
    public function services()
    {
        return $this->belongsToMany(Service::class, 'promotion_service')
                    ->withTimestamps();
    }

    // Itinerarios polimórficos (días / horarios)
    public function itineraries()
    {
        return $this->morphMany(Itinerary::class, 'itineraryable')
                    ->orderBy('day_number')
                    ->orderBy('start_time');
    }

    // Scope para promociones activas y vigentes
    public function scopeActive($query)
    {
        $today = Carbon::today()->toDateString();

        return $query->where('status', 'active')
                     ->whereDate('start_date', '<=', $today)
                     ->whereDate('end_date',   '>=', $today);
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];
}
