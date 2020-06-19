<?php

namespace App\Http\Controllers;

use App\Libs\HotSpots;
use App\Libs\LegacyApi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ApiController extends Controller
{
    public function getMobileContributors()
    {
        $contributors = LegacyApi::getMobileContributors();

        return \Response::json($contributors);
    }

    public function getModis()
    {
        if(env('APP_ENV') === 'production'){
            $exists = Redis::get('modis');
            if($exists){
                $response = json_decode($exists);
            } else {
                $response = HotSpots::getModis();


                if(!empty($response)){
                    Redis::set('modis', json_encode($response),'EX', 1080);
                }
            }
        } else {
            $response = HotSpots::getModis();
        }

        return \Response::json($response);
    }

    public function getVIIRS()
    {
        if(env('APP_ENV') === 'production'){
            $exists = Redis::get('VIIRS');
            if($exists){
                $response = json_decode($exists);
            } else {
                $response = HotSpots::getVIIRS();


                if(!empty($response)){
                    Redis::set('VIIRS', json_encode($response),'EX', 1080);
                }
            }
        } else {
            $response = HotSpots::getVIIRS();
        }

        return \Response::json($response);
    }

}
