<?php

namespace App\Http\Controllers;

class GenericController extends Controller
{
    public function getIndex()
    {
        return view('index');
    }

    public function getAbout()
    {
        return view('about');
    }

    public function getInformation()
    {
        return view('information');
    }

    public function getManifest()
    {
        return view('manifest');
    }
}
