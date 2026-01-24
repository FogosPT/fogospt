<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ParishTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/freguesias.json');
        $data = json_decode($json);

        foreach ($data->rows as $item) {
            $district = DB::table('districts')->where('dico', $item->value->dId)->first();
            $county = DB::table('counties')->where('dico', $item->value->cId)->first();

            DB::table('parishes')->insert([
                'dicofre' => $item->key,
                'name' => $item->value->name,
                'district_id' => $district->id,
                'county_id' => $county->id,
            ]);
        }
    }
}
