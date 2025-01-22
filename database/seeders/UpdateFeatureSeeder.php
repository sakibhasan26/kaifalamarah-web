<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Admin\AppSettings;
use App\Models\Admin\BasicSettings;
class UpdateFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(BasicSettings::first()) {
            BasicSettings::first()->update([
                'web_version'       => "3.3.0",
            ]);
        }
        if(AppSettings::first()){
            AppSettings::first()->update(['version' => '3.3.0']);
        }
    }
}
