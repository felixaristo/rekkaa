<?php

use App\Model\Setting\SettingModel;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        SettingModel::insert([
            [
                'setting_id' => 1,
                'setting_key' => 'ADDRESS',
                'setting_value' => 'Jl. Sultan Iskandar Muda No.18 i, RT 1/RW 2
                <br>Kebayoran Lama Selatan, Kec. Kebayoran Lama
                , Kota Jakarta Selatan 12240',
                'setting_description' => ''
            ]
        ]);
    }
}
