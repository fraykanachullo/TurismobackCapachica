<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'portal_id', 'slug', 'title', 'content',
        'language', 'published_at'
    ];

    public function portal()
    {
        return $this->belongsTo(Portal::class);
    }
}
