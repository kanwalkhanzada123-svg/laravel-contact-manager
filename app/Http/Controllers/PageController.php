<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function showAbout()
    {
        $userName = "Maria";
        return view('about', ['name' => $userName]);
    }
}