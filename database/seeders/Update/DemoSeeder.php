<?php

namespace Database\Seeders\Update;

use App\Models\Admin\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = Language::whereNot('code','en')->pluck('code');

        $sections = DB::table('category_types')->get();

        foreach ($sections as $section) {
            // Decode the JSON data in the `value` column
            $data = json_decode($section->data, true);

            // Check and translate main `language` object
            if (isset($data['language']['en'])) {
                $keysToTranslate = array_keys($data['language']['en']);
                foreach ($languages as $lang) {
                    $tr = new GoogleTranslate($lang);
                    if (!isset($data['language'][$lang])) {
                        $data['language'][$lang] = [];
                    }

                    foreach ($keysToTranslate as $key) {
                        $data['language'][$lang][$key] = $tr->translate($data['language']['en'][$key] ?? '');

                }
            }

            // Check if `items` object exists and translate its language data
            if (isset($data['items']) && is_array($data['items'])) {
                foreach ($data['items'] as $itemKey => $item) {
                    if (isset($item['language']['en'])) {
                        $itemKeysToTranslate = array_keys($item['language']['en']);
                        foreach ($languages as $lang) {
                            $tr = new GoogleTranslate($lang);
                            if (!isset($data['items'][$itemKey]['language'][$lang])) {
                                $data['items'][$itemKey]['language'][$lang] = [];
                            }

                            foreach ($itemKeysToTranslate as $key) {
                                $data['items'][$itemKey]['language'][$lang][$key] = $tr->translate($item['language']['en'][$key] ?? '');
                            }
                        }
                    }
                }
            }

            // Convert back to minified JSON and update the database
            DB::table('category_types')->where('id', $section->id)->update([
                'data' => json_encode($data, JSON_UNESCAPED_UNICODE)
            ]);
        }

        $this->command->info("Translations have been saved in the sitesection table with minified JSON format.");

    }
}
}
