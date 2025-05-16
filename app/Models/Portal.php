<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Portal extends Model
{
    protected $fillable = [
        'name', 'subdomain', 'default_language', 'logo_url',
        'primary_color', 'secondary_color', 'font_family', 'layout_template'
    ];

    public function design()
    {
        return $this->hasOne(PortalDesign::class);
    }
}
