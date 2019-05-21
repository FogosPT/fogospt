<?php

use Illuminate\Database\Seeder;

class DistrictTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
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
