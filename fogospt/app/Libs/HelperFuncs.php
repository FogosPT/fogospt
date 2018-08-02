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
        $cardinalDirections = array(
            'N' => array(348.75, 360),
            'N2' => array(0, 11.25),
            'NNE' => array(11.25, 33.75),
            'NE' => array(33.75, 56.25),
            'ENE' => array(56.25, 78.75),
            'E' => array(78.75, 101.25),
            'ESE' => array(101.25, 123.75),
            'SE' => array(123.75, 146.25),
            'SSE' => array(146.25, 168.75),
            'S' => array(168.75, 191.25),
            'SSO' => array(191.25, 213.75),
            'SO' => array(213.75, 236.25),
            'OSO' => array(236.25, 258.75),
            'O' => array(258.75, 281.25),
            'ONO' => array(281.25, 303.75),
            'NO' => array(303.75, 326.25),
            'NNO' => array(326.25, 348.75)
        );
        foreach ($cardinalDirections as $dir => $angles) {
            if ($deg >= $angles[0] && $deg < $angles[1]) {
                $cardinal = str_replace("2", "", $dir);
            }
        }
        return $cardinal;
    }
}