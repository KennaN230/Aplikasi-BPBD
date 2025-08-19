<?php

namespace App\Http\Controllers;

use App\Models\Kejadian;

class KejadianController extends Controller
{
    public function index()
    {
        $kejadian = Kejadian::all();
        return view('formKejadian', compact('kejadian'));
    }
}
