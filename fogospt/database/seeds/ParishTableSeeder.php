<?php

use Illuminate\Database\Seeder;

class ParishTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $json = \Illuminate\Support\Facades\File::get('database/data/freguesias.json');
        $data = json_decode($json);

        foreach ($data->rows as $item) {
            $district = \App\District::where('dico', $item->value->dId)->first();
            $county   = \App\County::where('dico', $item->value->cId)->first();

            \App\Parish::create([
                'dicofre'        => $item->key,
                'name'        => $item->value->name,
                'district_id' => $district->id,
                'county_id'   => $county->id,
            ]);
        }
    }
}
