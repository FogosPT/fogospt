<?php
/**
 * Created by PhpStorm.
 * User: tomahock
 * Date: 03/05/2018
 * Time: 19:08
 */

namespace App\Libs;

use App\Libs\Interfaces\ILegacyApi;
use GuzzleHttp;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;


class LegacyApi implements ILegacyApi
{
    private $url = 'https://api-beta.fogos.pt';

    public function getResults(string $endpoint) : array
    {
        return $this->performCall($this->url .$endpoint);
    }

    public function getResultById(string $endpoint, string $id) : array
    {
        return $this->performCall($this->url .$endpoint . $id);
    }

    private function performCall(string $endpoint)
    {
        try {
            $client = new GuzzleHttp\Client();
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
