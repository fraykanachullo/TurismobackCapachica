<?php

namespace App\Http\Controllers\Turista;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackageController extends Controller
{
    public function index()
    {
        return Package::where('status', 'active')->with('company')->paginate(10);
    }
}
