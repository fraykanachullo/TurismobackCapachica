<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * URIs que se eximen de la verificación CSRF.
     *
     * @var array<int,string>
     */
    protected $except = [
        'api/cart',
        'api/cart/*',
        'api/cart/summary',
        'api/turista/checkout',
    ];
}
