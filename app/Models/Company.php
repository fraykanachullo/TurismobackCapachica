<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Company extends Model implements Auditable
{
    use AuditableTrait;
    protected $fillable = [
        'user_id',
        'business_name',
        'trade_name',
        'service_type',
        'contact_email',
        'phone',
        'website',
        'description',
        'logo_url',
        'status',
        'verified_at',
        'ruc',
        'location_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function packages()
    {
        return $this->hasMany(Package::class);
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }
}