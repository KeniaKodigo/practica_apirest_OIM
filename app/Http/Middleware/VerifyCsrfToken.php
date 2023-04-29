<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //rutas para ofrecer un token (ofreciendo el permiso para los datos)
        'http://localhost/pracica_apirest_oim/public/registrarCliente'
    ];
}
