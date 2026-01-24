<?php
/**
 * Created by PhpStorm.
 * User: tomahock
 * Date: 01/08/2018
 * Time: 18:09
 */

namespace App\Libs;


class HelperFuncs
{
    static public function wind_cardinals($deg)
    {
        $cardinal = null;
        $cardinalDirections = [
            'N' => [348.75, 360],
            'N2' => [0, 11.25],
            'NNE' => [11.25, 33.75],
            'NE' => [33.75, 56.25],
            'ENE' => [56.25, 78.75],
            'E' => [78.75, 101.25],
            'ESE' => [101.25, 123.75],
            'SE' => [123.75, 146.25],
            'SSE' => [146.25, 168.75],
            'S' => [168.75, 191.25],
            'SSO' => [191.25, 213.75],
            'SO' => [213.75, 236.25],
            'OSO' => [236.25, 258.75],
            'O' => [258.75, 281.25],
            'ONO' => [281.25, 303.75],
            'NO' => [303.75, 326.25],
            'NNO' => [326.25, 348.75]
        ];
        foreach ($cardinalDirections as $dir => $angles) {
            if ($deg >= $angles[0] && $deg < $angles[1]) {
                return str_replace("2", "", $dir);
            }
        }

        return $cardinal;
    }
}