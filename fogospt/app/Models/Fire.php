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
            $baseResults = self::where('created', '>', new UTCDateTime(time() - 64800))
                            ->orderBy('dateTime', 'DESC')
                            ->get();

            foreach ($baseResults as $fireInfo) {
                $fireInfo['location'] = $fireInfo['location'] . ' - ' . $fireInfo['localidade'];
                $results[] = $fireInfo;
            }
            return $results;


            // get fire that are on going
            $onGoingFires = self::where('created', '>', new UTCDateTime(time() - 1984800))
                            ->where('created', '<', new UTCDateTime(time() - 64800))
                            ->where('status', 'Em curso')
                            ->orderBy('dateTime', 'DESC')
                            ->get();

            foreach ($onGoingFires as $fireInfo) {
                $fireInfo['location'] = $fireInfo['location'] . ' - ' . $fireInfo['localidade'];
                $results[] = $fireInfo;
            }

            // get fire that are on going
            $importantFires = self::where('created', '>', new UTCDateTime(time() - 344800))
                            ->where('created', '<', new UTCDateTime(time() - 64800))
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
