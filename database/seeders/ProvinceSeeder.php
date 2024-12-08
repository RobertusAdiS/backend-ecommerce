<?php

namespace Database\Seeders;

use App\Models\Province;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class ProvinceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //Feching data from rajaongkir
        $response = Http::withHeaders([
            'key' => config('services.rajaongkir.key')
        ])->get('https://api.rajaongkir.com/starter/province');
        //loop data
        foreach ($response['rajaongkir']['results'] as $province) {
            //insert data to database
            Province::create([
                'province_id' => $province['province_id'],
                'name' => $province['province']
            ]);
        }
    }
}
