<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GempaController extends Controller
{
    public function index()
    {
        return view('formGempa'); 
    }
}
