<?php

namespace Database\Seeders\Admin;

use Illuminate\Database\Seeder;
use App\Models\Admin\AppSettings;
use App\Models\Admin\AppOnboardScreens;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AppSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'version'             => '3.3.0',
            'splash_screen_image' => 'b42f02df-f69e-4d4b-9890-efb69502244d.webp',
            'url_title'           => 'App Urls',
            'android_url'         => 'https://play.google.com/store',
            'iso_url'             => 'https://www.apple.com/app-store',
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s'),
        ];


        AppSettings::firstOrCreate($data);

        $onboard =[
            [
              'id'           => 1,
              'title'        => "We help the kids to grow up in the right way",
              'sub_title'    => "The tone and elucidates the of system the universal village pink like so to her a try eye. of notice his a associates.",
              'image'        => 'f951bc18-854a-4f04-8848-08633d030781.webp',
              'status'       => 1,
              'last_edit_by' => 1,
              'created_at'   => date('Y-m-d H:i:s'),
              'updated_at'   => date('Y-m-d H:i:s'),
            ]
          ];
        AppOnboardScreens::insert($onboard);
    }
}
