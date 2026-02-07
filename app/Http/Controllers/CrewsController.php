<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CrewsController extends Controller
{
    public function index()
    {
        return view('crews.index');
    }
}
