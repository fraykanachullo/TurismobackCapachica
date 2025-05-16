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
      'type',
      'description',
      'location',              // texto libre (dirección)
      'price',
      'policy_cancellation',
      'capacity',
      'duration',
      'status',
      'published_at',
    ];

    public function company() { return $this->belongsTo(Company::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function zone()     { return $this->belongsTo(Location::class, 'location_id'); }
    public function media()    { return $this->hasMany(ServiceMedia::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
    public function reviews()      { return $this->hasMany(Review::class); }
}
