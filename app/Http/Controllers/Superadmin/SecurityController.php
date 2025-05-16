<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SecurityLog;
use App\Models\Audit; // Si usas auditoría, sino elimina esta línea

class SecurityController extends Controller
{
    public function logs()
    {
        // Traer logs con info de usuario (opcional)
        return SecurityLog::with('user')->latest()->limit(100)->get();
    }

    public function auditTrail()
    {
        // Si tienes modelo Audit y tabla creada
        return Audit::latest()->limit(100)->get();
    }
}
