<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //fetching data from rajaongkir
        $response = Http::withHeaders([
            'key' => config('services.rajaongkir.key')
        ])->get('https://api.rajaongkir.com/starter/city');
        //loop data
        foreach ($response['rajaongkir']['results'] as $city) {
            //insert data to database
            City::create([
                'province_id' => $city['province_id'],
                'city_id' => $city['city_id'],
                'city_name' => $city['city_name'] . ' - ' . '{' . $city['type'] . '}',
            ]);
        }
    }
}
