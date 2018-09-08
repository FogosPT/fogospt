<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\BSON\UTCDateTime;

class History extends Eloquent
{
    protected $connection = 'historyStatus';
    protected $collection  = 'data';

    public static function getLastRecordsById($id, $limit = 50)
    {
        try {
            $results = [];
            $queryRecords = self::where('id', $id)
                                ->orderBy('created', 'DESC')
                                ->get()->take($limit);
            foreach ($queryRecords as $queryRecord) {
                $queryRecord['label'] = $queryRecord['created']->toDateTime()->format('H:i');
                unset($queryRecord['created'], $r['updated']);
                $results[] = $queryRecord;
            }

            $results[] =  [
                    'label' => Fire::getFire($id)->hour,
                    'man' => 0,
                    'terrain' => 0,
                    'aerial' => 0
            ];
            return array_reverse($results);
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }
}
