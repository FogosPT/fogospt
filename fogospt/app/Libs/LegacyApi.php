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
    private static function getClient()
    {
        $client = new GuzzleHttp\Client();

        return $client;
    }
}