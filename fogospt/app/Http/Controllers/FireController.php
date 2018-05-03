<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FireController extends Controller
{
    public function get(Request $request)
    {
        if(!$request->exists('id')){
            return view('index');
        }
    }
}
