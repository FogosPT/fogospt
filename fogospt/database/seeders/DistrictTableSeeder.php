<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DistrictTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/distritos.json');
        $data = json_decode($json);

        foreach ($data->rows as $district) {
            \DB::table('districts')->insert([
                'di' => $district->key,
                'name' => $district->value->name,
            ]);
        }
    }
}
