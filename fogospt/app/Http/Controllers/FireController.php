<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libs\LegacyApi;

class FireController extends Controller
{
    public function get(Request $request, $id)
    {
        if(!$id){
            return view('index');
        } else {
            return view('index');
        }

        $fire = LegacyApi::getFire($id);

        return view('index-fire', $fire);
    }
}
