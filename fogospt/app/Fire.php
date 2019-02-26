<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fire extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'coords',
        'dateTime',
        'date',
        'hour',
        'location',
        'aerial',
        'terrain',
        'man',
        'district',
        'concelho',
        'freguesia',
        'lat',
        'lng',
        'naturezaCode',
        'natureza',
        'statusCode',
        'statusColor',
        'status',
        'important',
        'localidade',
        'active',
        'sadoId',
        'sharepointId',
        'extra',
        'statusOld'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
    ];

    const CREATED_AT = 'created';
    const UPDATED_AT = 'updated';
}
