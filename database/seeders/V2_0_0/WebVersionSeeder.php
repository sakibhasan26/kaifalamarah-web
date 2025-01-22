<?php

namespace Database\Seeders\V2_0_0;

use Illuminate\Database\Seeder;
use App\Models\Admin\AppSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WebVersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $app_settings = array(
            'version' => '2.5.0'
        );

        $appSettings = AppSettings::first();
        $appSettings->update($app_settings);
    }
}
