<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DistrictTableSeeder extends Seeder
{
    public function run()
    {
        $json = \Illuminate\Support\Facades\File::get('database/data/distritos.json');
        $data = json_decode($json);

        foreach ($data->rows as $district) {
            \App\District::create([
                'di' => $district->key,
                'name' => $district->value->name,
            ]);
        }
    }
}
