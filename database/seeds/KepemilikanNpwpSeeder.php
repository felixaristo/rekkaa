<?php

use App\Model\Master\KepemilikanNpwpModel;
use Illuminate\Database\Seeder;

class KepemilikanNpwpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        KepemilikanNpwpModel::insert([
            [
                'kepemilikannpwp_id' => 1,
                'kepemilikannpwp_code' => 'NPWP',
                'kepemilikannpwp_name' => 'NPWP',
                'kepemilikannpwp_value' => 1,
                'kepemilikannpwp_active' => 1,
            ],
            [
                'kepemilikannpwp_id' => 2,
                'kepemilikannpwp_code' => 'NO-NPWP',
                'kepemilikannpwp_name' => 'Tidak Ber-NPWP',
                'kepemilikannpwp_value' => 2,
                'kepemilikannpwp_active' => 1,
            ]
        ]);
    }
}
