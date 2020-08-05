<?php
namespace App\Libs;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Redis;

class OpenWeatherClient
{
    private static $weatherUrl = 'api.openweathermap.org/data/2.5/weather?';
    public static function getMeteoByFire($lat, $lng)
    {
        if (env('APP_ENV') === 'production') {
            $exists = Redis::get('weather:' . $lat . ':' . $lng);
            if ($exists) {
                return json_decode($exists, true);
            } else {
                $client = new GuzzleHttp\Client();
                $weatherUrl = self::$weatherUrl . 'lat=' . $lat . '&lon=' . $lng . '&APPID=' . env('OPENWEATHER_API') . '&units=metric&lang=pt';

                try {
                    $response = $client->request('GET', $weatherUrl);

                } catch (ClientException $e) {
                    return ['error' => $e->getMessage()];
                } catch (RequestException $e) {
                    return ['error' => $e->getMessage()];
                }

                $body = $response->getBody();
                $result = json_decode($body->getContents(), true);

                Redis::set('weather:' . $lat . ':' . $lng, json_encode($result), 'EX', 10800);

                return $result;
            }
        }
    }
}