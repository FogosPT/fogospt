<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

use App\Models\Fire;
class ApiController extends Controller
{
    private $weatherUrl = 'api.openweathermap.org/data/2.5/weather?';

    public function getFires()
    {
        try {
           return [
               "success" => true,
               "data" => Fire::getAll()
           ];
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getWarnings()
    {
        try {
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getWeekStats()
    {
        try {
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }


    public function getFire($id)
    {
        try {
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getRiskByFire()
    {
        try {
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getStatusByFire()
    {
        try {
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getStats8HoursToday()
    {
        try {
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getStats8HoursYesterday()
    {
        try {
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getStatsLastNight()
    {
        try {
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }


    public function getMeteoByFire($lat, $lng)
    {
        if (env('APP_ENV') === 'production') {
            $exists = Redis::get('weather:'.$lat.':'.$lng);
            if ($exists) {
                return json_decode($exists, true);
            } else {
                $client = self::getClient();
                $weatherUrl = self::$weatherUrl . 'lat=' . $lat . '&lon=' . $lng. '&APPID='. env('OPENWEATHER_API') . '&units=metric&lang=pt';

                try {
                    $response = $client->request('GET', $weatherUrl);
                } catch (ClientException $e) {
                    return ['error' => $e->getMessage()];
                } catch (RequestException $e) {
                    return ['error' => $e->getMessage()];
                }

                $body = $response->getBody();
                $result = json_decode($body->getContents(), true);

                Redis::set('weather:'.$lat.':'.$lng, json_encode($result), 'EX', 10800);

                return $result;
            }
        }
    }
}
