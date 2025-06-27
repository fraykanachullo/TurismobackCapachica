<?php

// app/Models/Service.php
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

// modelo trait para auditar
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Service extends Model implements Auditable
{
    use AuditableTrait;

    protected $fillable = [
      'company_id',
      'category_id',
      'location_id',
      'title',
      'slug',
     
      'description',
      'ubicacion_detallada',     // Nuevo campo para la ubicación detallada
      'price',
      'policy_cancellation',
      'capacity',
      'duration',
      'status',
      'published_at',
      'is_active',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function zone()     { return $this->belongsTo(Location::class, 'location_id'); }
    public function media()    { return $this->hasMany(ServiceMedia::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
    public function reviews()      { return $this->hasMany(Review::class); }
    
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'promotion_service')
                    ->withTimestamps();
    }

    // Itinerarios polimórficos
    public function itineraries()
    {
        return $this->morphMany(Itinerary::class, 'itineraryable')
                    ->orderBy('day_number')
                    ->orderBy('start_time');
    }
    
}
