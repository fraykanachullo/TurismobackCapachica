<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class User extends Authenticatable implements CanResetPassword, Auditable
{
    use HasApiTokens, Notifiable, HasRoles, HasFactory, AuditableTrait;

    /**
     * Campos asignables
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'foto',        // coincide con tu columna en la migración
        'google_id',   // si usas login social
        'estado',      // activo / bloqueado
    ];

    /**
     * Campos ocultos en el JSON
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Scope: sólo usuarios con rol "turista"
     */
    public function scopeTuristas($query)
    {
        return $query->role('turista');
    }

    /**
     * Relaciones
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function behaviors()
    {
        return $this->hasOne(UserBehavior::class);
    }

    /**
     * Accesor: calificación promedio de reviews
     */
    public function getAverageRatingAttribute()
    {
        return round($this->reviews()->avg('rating'), 2);
    }
}
