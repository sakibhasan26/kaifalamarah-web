<?php

namespace Database\Seeders\V3_1_0;

use App\Models\CategoryType;
use App\Models\Admin\Language;
use Illuminate\Database\Seeder;

class UpdateCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        $blog_categories_from_db = CategoryType::get();
        $languages = Language::get();

        foreach ($blog_categories_from_db as $blog_category) {
            $languageTranslations = array();
            foreach ($languages as $language) {
                $languageCode = $language->code;
                $translation = ($languageCode == 'en') ? $blog_category->name : null;
                $languageTranslations[$languageCode] = array('name' => $translation);
            }
            $blog_category->data = array('language' => $languageTranslations);

            $blog_category->save();
        }
    }


}
