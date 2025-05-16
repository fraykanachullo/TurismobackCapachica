<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'company_id', 'title', 'description', 'price', 'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
