<?php

use App\Model\Master\TunjanganJabatanModel;
use Illuminate\Database\Seeder;

class TunjanganJabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        TunjanganJabatanModel::insert([
            [
                'tunjanganjabatan_id' => 1,
                'tunjanganjabatan_year' => 2022,
                'tunjanganjabatan_rate' => 5,
                'tunjanganjabatan_maximum_allowance' => 500000,
                'tunjanganjabatan_active' => 1,
            ]
        ]);
    }
}
