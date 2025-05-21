<?php

namespace App\Http\Controllers\Publico;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PublicPackageController extends Controller
{
    public function index()
    {
        return Package::where('status', 'active')->with('services')->get();
    }
}
