<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index(){
    	 $halaman = 'about';
        return view('pages.about', compact('halaman'));
    }
}
