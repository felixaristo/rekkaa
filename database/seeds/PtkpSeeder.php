<?php

use App\Model\Master\PtkpModel;
use Illuminate\Database\Seeder;

class PtkpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        PtkpModel::insert([
            [
                'ptkp_id' => 1,
                'ptkp_marriage_status' => 'Tidak Kawin (TK)',
                'ptkp_description' => 'TK/0 (tanpa tanggungan)',
                'ptkp_rate' => 54000000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 2,
                'ptkp_marriage_status' => 'Tidak Kawin (TK)',
                'ptkp_description' => 'TK/1 (1 tanggungan)',
                'ptkp_rate' => 58500000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 3,
                'ptkp_marriage_status' => 'Tidak Kawin (TK)',
                'ptkp_description' => 'TK/2 (2 tanggungan)',
                'ptkp_rate' => 63000000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 4,
                'ptkp_marriage_status' => 'Tidak Kawin (TK)',
                'ptkp_description' => 'TK/3 (3 tanggungan)',
                'ptkp_rate' => 67500000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 5,
                'ptkp_marriage_status' => 'Kawin (K)',
                'ptkp_description' => 'K0 (tanpa tanggungan)',
                'ptkp_rate' => 58500000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 6,
                'ptkp_marriage_status' => 'Kawin (K)',
                'ptkp_description' => 'K1 (1 tanggungan)',
                'ptkp_rate' => 63000000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 7,
                'ptkp_marriage_status' => 'Kawin (K)',
                'ptkp_description' => 'K2 (2 tanggungan)',
                'ptkp_rate' => 67500000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 8,
                'ptkp_marriage_status' => 'Kawin (K)',
                'ptkp_description' => 'K3 (3 tanggungan)',
                'ptkp_rate' => 72000000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 9,
                'ptkp_marriage_status' => 'Kawin dengan penghasilan istri digabung (K/I)',
                'ptkp_description' => 'K/I/0 (tanpa tanggungan)',
                'ptkp_rate' => 54000000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 10,
                'ptkp_marriage_status' => 'Kawin dengan penghasilan istri digabung (K/I)',
                'ptkp_description' => 'K/I/1 (1 tanggungan)',
                'ptkp_rate' => 117000000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 11,
                'ptkp_marriage_status' => 'Kawin dengan penghasilan istri digabung (K/I)',
                'ptkp_description' => 'K/I/2 (2 tanggungan)',
                'ptkp_rate' => 121500000,
                'ptkp_active' => 1,
            ],
            [
                'ptkp_id' => 12,
                'ptkp_marriage_status' => 'Kawin dengan penghasilan istri digabung (K/I)',
                'ptkp_description' => 'K/I/3 (3 tanggungan)',
                'ptkp_rate' => 126000000,
                'ptkp_active' => 1,
            ],
        ]);
    }
}
