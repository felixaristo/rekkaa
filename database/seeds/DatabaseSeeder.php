<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(KepemilikanNpwpSeeder::class);
        $this->call(PtkpSeeder::class);
        $this->call(SettingSeeder::class);
        $this->call(Tarif21Seeder::class);
        $this->call(TunjanganJabatanSeeder::class);
        $this->call(BpjsRateSeeder::class);
        $this->call(TarifNonnpwpSeeder::class);
    }
}
