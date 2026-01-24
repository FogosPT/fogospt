<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CountyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/data/concelhos.json');
        $data = json_decode($json);

        foreach ($data->rows as $item) {
            $district = DB::table('districts')->where('dico', $item->value->dId)->first();
            DB::table('counties')->insert([
                'dico' => $item->key,
                'name' => $item->value->name,
                'district_id' => $district->id,
            ]);
        }
    }
}
