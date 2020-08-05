<?php
/**
 * Created by PhpStorm.
 * User: tomahock
 * Date: 03/05/2018
 * Time: 19:08
 */

namespace App\Libs;

use App\Libs\Enums\FogosApiEndpoints;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Redis;


class LegacyApi
{
    private static $url = 'https://api-beta.fogos.pt';

    public static function getResults(FogosApiEndpoints $endpoint)
    {
        return self::performCall(self::url .$endpoint);
    }

    public static function getResultById(FogosApiEndpoints $endpoint, $id)
    {
        return self::performCall(self::url .$endpoint . $id);
    }

    private function performCall($endpoint)
    {
        try {
            $client = new GuzzleHttp\Client(['verify'          => false]);
            dd($endpoint);
            $response = $client->request('GET', $endpoint);
            $body = $response->getBody();
            return json_decode($body->getContents(), true);
        } catch (ClientException $e) {
            return ['error' => $e->getMessage()];
        } catch (RequestException $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
