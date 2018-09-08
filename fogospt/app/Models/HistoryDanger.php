<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class HistoryDanger extends Eloquent
{
    protected $connection = 'historyDanger';
    protected $collection  = 'data';

    public static function getByCounty($county)
    {
        try {
            return self::where('concelho', $county)
                                ->orderBy('created', 'DESC')
                                ->first();
        } catch (Exception $ex) {
            return ['error' => $ex->getMessage()];
        }
    }
}
