<?php

use App\Model\Master\TarifNonNpwpModel;
use Illuminate\Database\Seeder;

class TarifNonnpwpSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TarifNonNpwpModel::insert([
            [
                'tarifnonnpwp_id' => 1,
                'tarifnonnpwp_taxtype' => 'PPh21',
                'tarifnonnpwp_rate' => 120,
            ],
            [
                'tarifnonnpwp_id' => 2,
                'tarifnonnpwp_taxtype' => 'PPh23',
                'tarifnonnpwp_rate' => 100,
            ],
            [
                'tarifnonnpwp_id' => 3,
                'tarifnonnpwp_taxtype' => 'PPh42',
                'tarifnonnpwp_rate' => 100,
            ]
        ]);
    }
}
