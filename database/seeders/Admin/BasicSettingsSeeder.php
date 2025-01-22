<?php

namespace Database\Seeders\Admin;

use App\Models\Admin\BasicSettings;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasicSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'site_name'       => "kaifala marah",
            'web_version'       => "3.3.0",
            'site_title'      => "Develop Your Dreams",
            'base_color'      => "#ea5455",
            'secondary_color' => "#ea5455",
            'otp_exp_seconds' => "3600",
            'timezone'        => "Asia/Dhaka",
            'site_logo_dark'  => "05968ef3-b5bd-4a38-90e2-2fd8ddbd128a.webp",
            'site_logo'       => "f095db7a-0821-4a68-a144-5f4c9e8226a3.webp",
            'site_fav_dark'   => "02f0526e-5d14-40da-a496-b36df02978bb.webp",
            'site_fav'        => "5334d33b-d486-458c-b6ad-f25dce1c6f03.webp",
            'user_registration'  => 1,
            'email_verification' => 1,
            'agree_policy'       => 1,
            'broadcast_config'  => [
                "method" => "pusher",
                "app_id" => "",
                "primary_key" => "",
                "secret_key" => "",
                "cluster" => ""
            ],
            'mail_config'       => [
                "method" => "",
                "host" => "",
                "port" => "465",
                "encryption" => "",
                "password" => "",
                "username" => "",
                "from" => "",
                "app_name" => "kaifala marah",
            ],
            'push_notification_config'  => [
                "method" => "pusher",
                "instance_id" => "",
                "primary_key" => ""
            ],
        ];

        BasicSettings::firstOrCreate($data);
    }
}
