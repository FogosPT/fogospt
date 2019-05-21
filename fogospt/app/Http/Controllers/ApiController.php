<?php

namespace App\Http\Controllers;

use App\Libs\LegacyApi;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getMobileContributors()
    {
        $contributors = LegacyApi::getMobileContributors();

        return \Response::json($contributors);
    }

}
