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

    public static function getByStatus($status)
    {
        try {
            $searchParam = "";
            switch ($status) {
                case 'active':
                    return self::getActiveFires();
                break;
                case 'almost-done':
                    $searchParam = "EM RESOLUÇÃO";
                break;
                case 'done':
                    $searchParam = "CONCLUSÃO";
                break;
                case 'alert':
                    $searchParam = new \MongoDB\BSON\Regex('/ALERTA/');
                break;
            }
            return self::where('date', date('d-m-Y', time()))
                ->where('status', $searchParam)
                ->get();
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    public static function getFire($id)
    {
        try {
            return self::where('id', $id)->first();
        } catch (Exception $ex) {
            return ["error" => $ex->getMessage()];
        }
    }

    public static function getWeekStats()
    {
        try {
            $results = [];
            for ($i = 0; $i <= 8; $i++) {
                $start =  \Carbon\Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d 00:00'))->subDays($i);
                $end = \Carbon\Carbon::createFromFormat('Y-m-d H:i', date('Y-m-d 23:59'))->subDays($i);
                $results[] = array(
                    'label' => $end->format('Y-m-d'),
                    'total' => self::getTotalStatsTimestampRaw($start, $end),
                    'false' => self::getTotalStatsTimestampRaw($start, $end, ['Falso Alarme', 'Falso Alerta']),
                );
            }
            return array_reverse($results);
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public static function getTotalStatsTimestampRaw($start, $end, $status = [])
    {
        try {
            $query = self::where('dateTime', '>', $start)->where('dateTime', '<', $end);
            if (!empty($status)) {
                $query->whereIn('status', $status);
            }
            return $query->count();
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
    private static function getActiveFires()
    {
        try {
            $results = [];
            $currentTimeStamp = \Carbon\Carbon::now();
            $queryParameters = [
                [
                    'timeToSubtract' => 984800,
                    'status' => 'Em Curso'
                ],
                [
                    'timeToSubtract' => 184800,
                    'status' => 'Despacho de 1º Alerta'
                ],
                [
                    'timeToSubtract' => 384800,
                    'status' => 'Despacho'
                ],
                [
                    'timeToSubtract' => 384800,
                    'status' => 'Chegada ao TO'
                ],

            ];
            foreach ($queryParameters as $queryParameter) {
                $queryResults = self::where('created', '>', $currentTimeStamp->subSeconds($queryParameter['timeToSubtract']))
                            ->where('status', $queryParameter['status'])
                            ->orderBy('dateTime', 'DESC')
                            ->get();
                foreach ($queryResults as $result) {
                    $results[] = $result;
                }
            }
            return $results;
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }
}
