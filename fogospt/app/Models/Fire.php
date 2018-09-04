<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use \MongoDB\BSON\UTCDateTime;

class Fire extends Eloquent
{
    protected $connection = 'fires';
    protected $collection  = 'newData';

    public static function getAll()
    {
        try {
            $results = [];
            $currentTimeStamp = \Carbon\Carbon::now();
            $baseResults = self::where('created', '>', $currentTimeStamp->subSeconds(64800))
                            ->orderBy('dateTime', 'DESC')
                            ->get();

            foreach ($baseResults as $fireInfo) {
                $fireInfo['location'] = $fireInfo['location'] . ' - ' . $fireInfo['localidade'];
                $results[] = $fireInfo;
            }


            // get fire that are on going
            $onGoingFires = self::where('created', '>', $currentTimeStamp->subSeconds(1984800))
                            ->where('created', '<', $currentTimeStamp->subSeconds(64800))
                            ->where('status', 'Em curso')
                            ->orderBy('dateTime', 'DESC')
                            ->get();

            foreach ($onGoingFires as $fireInfo) {
                $fireInfo['location'] = $fireInfo['location'] . ' - ' . $fireInfo['localidade'];
                $results[] = $fireInfo;
            }

            // get fire that are on going
            $importantFires = self::where('created', '>', $currentTimeStamp->subSeconds(344800))
                            ->where('created', '<', $currentTimeStamp->subSeconds(6480))
                            ->where('important', true)
                            ->orderBy('dateTime', 'DESC')
                            ->get();

            foreach ($importantFires as $fireInfo) {
                $fireInfo['location'] = $fireInfo['location'] . ' - ' . $fireInfo['localidade'];
                $results[] = $fireInfo;
            }
            return $results;
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }
}
