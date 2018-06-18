<?php
/**
 * Created by PhpStorm.
 * User: tomahock
 * Date: 03/05/2018
 * Time: 19:08
 */
namespace App\Libs;

use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;


class LegacyApi
{
    private static $url = 'https://api-beta.fogos.pt';
    private static $weatherUrl = 'api.openweathermap.org/data/2.5/weather?';

    private static function getClient()
    {
        $client = new GuzzleHttp\Client();

        return $client;
    }

    public static function getFires()
    {
        $client = self::getClient();

        $url = self::$url . '/new/fires';

        try {
            $response = $client->request('GET', $url);

        } catch (ClientException $e) {
            return ['error' => $e->getMessage()];
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }

        $body = $response->getBody();
        $result = json_decode($body->getContents(), true);

        return $result;
    }

    public static function getWarnings()
    {
        $client = self::getClient();

        $url = self::$url . '/v1/warnings';

        try {
            $response = $client->request('GET', $url);

        } catch (ClientException $e) {
            return ['error' => $e->getMessage()];
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }

        $body = $response->getBody();
        $result = json_decode($body->getContents(), true);

        return $result;
    }

    public static function getFire($id)
    {
        $client = self::getClient();

        $url = self::$url . '/fires?id=' . $id;

        try {
            $response = $client->request('GET', $url);

        } catch (ClientException $e) {
            return ['error' => $e->getMessage()];
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }

        $body = $response->getBody();
        $result = json_decode($body->getContents(), true);
        
        return $result;
    }

    public static function getRiskByFire($id)
    {
        $client = self::getClient();
        $url = self::$url . '/fires/danger?id=' . $id;

        try {
            $response = $client->request('GET', $url);
        } catch (ClientException $e) {
            return ['error' => $e->getMessage()];
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }

        $body = $response->getBody();
        $result = json_decode($body->getContents(), true);
        return $result;
    }

    public static function getNow()
    {
        $client = self::getClient();
        $url = self::$url . '/v1/now';

        try {
            $response = $client->request('GET', $url);
        } catch (ClientException $e) {
            return ['error' => $e->getMessage()];
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }

        $body = $response->getBody();
        $result = json_decode($body->getContents(), true);
        return $result;
    }

    public static function getStatusByFire($id)
    {
        $client = self::getClient();
        $url = self::$url . '/fires/status?id=' . $id;

        try {
            $response = $client->request('GET', $url);

        } catch (ClientException $e) {
            return ['error' => $e->getMessage()];
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }

        $body = $response->getBody();
        $result = json_decode($body->getContents(), true);
        return $result;
    }

    public static function getMeteoByFire($lat,$lng)
    {
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
        
            
        return $result;
    }
    
}
