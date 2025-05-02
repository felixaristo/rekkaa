<?php

use App\Model\Master\Tarif21Model;
use Illuminate\Database\Seeder;

class Tarif21Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Tarif21Model::insert([
            [
                'tarif21_id' => 1,
                'tarif21_year' => 2022, 
                'tarif21_description' => 'Hingga Rp60.000.000', 
                'tarif21_rate' => 5, 
                'tarif21_startincome' => 0, 
                'tarif21_endincome' => 60000000, 
                'tarif21_active' => 1
            ],
            [
                'tarif21_id' => 2,
                'tarif21_year' => 2022, 
                'tarif21_description' => 'Rp60.000.001 - Rp250.000.000', 
                'tarif21_rate' => 15, 
                'tarif21_startincome' => 60000001, 
                'tarif21_endincome' => 250000000, 
                'tarif21_active' => 1
            ],
            [
                'tarif21_id' => 3,
                'tarif21_year' => 2022, 
                'tarif21_description' => 'Rp250.000.000 - Rp500.000.000', 
                'tarif21_rate' => 25, 
                'tarif21_startincome' => 250000001, 
                'tarif21_endincome' => 500000000, 
                'tarif21_active' => 1
            ],
            [
                'tarif21_id' => 4,
                'tarif21_year' => 2022, 
                'tarif21_description' => 'Rp500.000.000 - Rp5.000.000.000', 
                'tarif21_rate' => 30, 
                'tarif21_startincome' => 500000001, 
                'tarif21_endincome' => 5000000000, 
                'tarif21_active' => 1
            ],
            [
                'tarif21_id' => 5,
                'tarif21_year' => 2022, 
                'tarif21_description' => 'Di atas Rp5.000.000.000', 
                'tarif21_rate' => 35, 
                'tarif21_startincome' => 5000000001, 
                'tarif21_endincome' => 9999999999999, 
                'tarif21_active' => 1
            ]
        ]);
    }
}
