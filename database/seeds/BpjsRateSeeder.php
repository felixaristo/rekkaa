<?php

use App\Model\Master\BpjsRateModel;
use Illuminate\Database\Seeder;

class BpjsRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        BpjsRateModel::insert([
            [
                'bpjsrate_id' => 1,
                'bpjsrate_category' => 'TK',
                'bpjsrate_code' => 'JKK',
                'bpjsrate_description' => 'JKK',
                'bpjsrate_rate' => 0.24,
                'bpjsrate_calculationtype' => 'PENAMBAH'
            ],
            [
                'bpjsrate_id' => 2,
                'bpjsrate_category' => 'TK',
                'bpjsrate_code' => 'JKM',
                'bpjsrate_description' => 'JKM',
                'bpjsrate_rate' => 0.3,
                'bpjsrate_calculationtype' => 'PENAMBAH'
            ],
            [
                'bpjsrate_id' => 3,
                'bpjsrate_category' => 'TK',
                'bpjsrate_code' => 'JPMin',
                'bpjsrate_description' => 'JP',
                'bpjsrate_rate' => 1,
                'bpjsrate_calculationtype' => 'PENGURANG'
            ],
            [
                'bpjsrate_id' => 4,
                'bpjsrate_category' => 'TK',
                'bpjsrate_code' => 'JHT',
                'bpjsrate_description' => 'JHT',
                'bpjsrate_rate' => 3.7,
                'bpjsrate_calculationtype' => 'PENAMBAH'
            ],
            [
                'bpjsrate_id' => 5,
                'bpjsrate_category' => 'TK',
                'bpjsrate_code' => 'JHTMin',
                'bpjsrate_description' => 'JHT',
                'bpjsrate_rate' => 2,
                'bpjsrate_calculationtype' => 'PENGURANG'
            ],
            [
                'bpjsrate_id' => 6,
                'bpjsrate_category' => 'KES',
                'bpjsrate_code' => 'JamKesMin',
                'bpjsrate_description' => 'JamKes',
                'bpjsrate_rate' => 1,
                'bpjsrate_calculationtype' => 'PENGURANG'
            ],
            [
                'bpjsrate_id' => 7,
                'bpjsrate_category' => 'KES',
                'bpjsrate_code' => 'JamKes',
                'bpjsrate_description' => 'JamKes',
                'bpjsrate_rate' => 4,
                'bpjsrate_calculationtype' => 'PENAMBAH'
            ],
            [
                'bpjsrate_id' => 8,
                'bpjsrate_category' => 'TK',
                'bpjsrate_code' => 'JP',
                'bpjsrate_description' => 'JP',
                'bpjsrate_rate' => 2,
                'bpjsrate_calculationtype' => 'PENAMBAH'
            ]
        ]);
    }
}
