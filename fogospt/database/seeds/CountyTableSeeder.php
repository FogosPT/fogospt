<?php

use Illuminate\Database\Seeder;

class CountyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $json = \Illuminate\Support\Facades\File::get('database/data/concelhos.json');
        $data = json_decode($json);

        foreach ($data->rows as $item) {
            $district = \App\District::where('dico', $item->value->dId)->first();
            \App\County::create([
                'dico'        => $item->key,
                'name'        => $item->value->name,
                'district_id' => $district->id,
            ]);
        }
    }
}
