<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name','type'];
    public function companies()
    {
      return $this->hasMany(Company::class);
    }
}
