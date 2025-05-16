<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'company_id', 'title', 'content', 'featured_image_url', 'status', 'published_at'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
