<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDbAndFireTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fire', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('coords');
            $table->timestamp('dateTime');
            $table->string('date');
            $table->string('hour');
            $table->string('location');
            $table->integer('aerial');
            $table->integer('terrain');
            $table->integer('man');
            $table->();
            $table->();
            $table->();
            $table->();
            $table->();
            $table->();
            $table->timestamps();
        });

//        'id',
//        'coords',
//        'dateTime',
//        'date',
//        'hour',
//        'location',
//        'aerial',
//        'terrain',
//        'man',
//        'district',
//        'concelho',
//        'freguesia',
//        'lat',
//        'lng',
//        'naturezaCode',
//        'natureza',
//        'statusCode',
//        'statusColor',
//        'status',
//        'important',
//        'localidade',
//        'active',
//        'sadoId',
//        'sharepointId',
//        'extra',
//        'statusOld'
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('db_and_fire');
    }
}
