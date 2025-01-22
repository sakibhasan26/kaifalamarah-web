<?php

namespace Database\Seeders\Update;
use App\Models\Admin\Language;
use Illuminate\Database\Seeder;

class LanguageUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = array(
            array('name' => 'Hindi','code' => 'hi','status' => '0','last_edit_by' => '1','created_at' => '2024-10-25 08:59:58','updated_at' => '2024-10-25 08:59:58','dir' => 'ltr'),
            array('name' => 'French','code' => 'fr','status' => '0','last_edit_by' => '1','created_at' => '2024-10-25 09:04:40','updated_at' => '2024-10-25 09:04:40','dir' => 'ltr')

        );

        foreach ($languages as $language) {
            // Check if the language code already exists
            if (!Language::where('code', $language['code'])->exists()) {
                // Insert the language if it doesn't exist
                Language::create($language);
            }

        }
    }
}
