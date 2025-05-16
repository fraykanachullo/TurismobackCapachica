<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
// modelo trait para auditar
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable implements CanResetPassword, Auditable  //auditable
{
    use HasApiTokens, Notifiable, HasRoles, HasFactory, AuditableTrait;

    protected $fillable = ['name', 'email', 'password', 'avatar_url', 'google_id'];  // Agregar 'google_id'

    protected $hidden = ['password', 'remember_token'];

    // Relación con la empresa (si aplica)
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    // Relación con las reservaciones
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Relación con las reseñas
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Relación con el comportamiento del usuario (si aplica)
    public function behaviors()
    {
        return $this->hasOne(UserBehavior::class);
    }
}

