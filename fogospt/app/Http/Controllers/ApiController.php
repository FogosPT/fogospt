<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Fire;
use App\Models\HistoryStatus;
use App\Models\History;
use App\Models\HistoryDanger;
use App\Models\Warning;

class ApiController extends Controller
{
    private $weatherUrl = 'api.openweathermap.org/data/2.5/weather?';

    public function getFires()
    {
        try {
            return [
               'success' => true,
               'data' => Fire::getAll()
            ];
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getFiresByStatus($status)
    {
        try {
            return [
                'success' => true,
                'data' => Fire::getByStatus($status)
            ];
        } catch (Exception $ex) {
            return  ['error' => $ex->getMessage()];
        }
    }

    public function getFire($id)
    {
        try {
            return [
                'success' => true,
                'data' => Fire::getFire($id)
            ];
        } catch (Exception $ex) {
            return  ['error' => $ex->getMessage()];
        }
    }

    public function getStatusByFire($fireId)
    {
        try {
            return [
                'success' => true,
                'data' => HistoryStatus::getLastRecordsById($fireId)
            ];
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }
    public function getDataByFire($fireId)
    {
        try {
            return [
                'success' => true,
                'data' => History::getLastRecordsById($fireId)
            ];
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getDangerByFire($fireId)
    {
        try {
            $fire = Fire::getFire($fireId);
            return [
                'success' => true,
                'data' => [
                    'fire' => $fire,
                    'danger' => HistoryDanger::getByCounty($fire->concelho)
                ]
            ];
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }
    public function getWarnings($limit = null)
    {
        try {
            return [
                'success' => true,
                'data' => Warning::getLast($limit)
            ];
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }

    public function getWeekStats()
    {
        try {
            return [
                'success' => true,
                'data' => Fire::getWeekStats()
            ];
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

    public function dummyMethod()
    {
    }
}
