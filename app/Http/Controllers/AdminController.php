<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function mahasiswa()
    {
        return view('home');
    }
}
