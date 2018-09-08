<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use MongoDB\BSON\UTCDateTime;

class Warning extends Eloquent
{
    protected $connection = 'warnings';
    protected $collection  = 'data';

    public static function getLast($limit = 50)
    {
        try {
            $queryRecords = self::orderBy('created', 'DESC')
                                ->get()->take($limit);
            foreach ($queryRecords as $queryRecord) {
                $queryRecord['label'] = $queryRecord['created']->toDateTime()->format('H:i');
                unset($queryRecord['created'], $queryRecord['updated']);
                $results[] = $queryRecord;
            }
            return $results;
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }
}
