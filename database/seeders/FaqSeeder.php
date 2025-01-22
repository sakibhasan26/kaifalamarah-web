<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faq_sections = array(
            array('category_id' => '1','question' => 'What Services Can I Outsource?','answer' => 'Maecenas tempus tellus eget condimentu oncussam mperngsu libero sit amet adipiscing ue sed ipsum. Nam quam nunc, blandit veluctus pulvinar. Maecenas tempus.','status' => '1','created_at' => '2023-03-02 13:56:05','updated_at' => '2023-03-02 14:36:22'),
            array('category_id' => '1','question' => 'How Are Completed Contracts Or Work Orders Signed?','answer' => 'Maecenas tempus tellus eget condimentu oncussam mperngsu libero sit amet adipiscing ue sed ipsum. Nam quam nunc, blandit veluctus pulvinar. Maecenas tempus.','status' => '1','created_at' => '2023-03-02 13:58:43','updated_at' => '2023-03-02 13:58:43'),
            array('category_id' => '1','question' => 'What Modes Of Payment Do You Accept?','answer' => 'Maecenas tempus tellus eget condimentu oncussam mperngsu libero sit amet adipiscing ue sed ipsum. Nam quam nunc, blandit veluctus pulvinar. Maecenas tempus.','status' => '1','created_at' => '2023-03-02 13:59:04','updated_at' => '2023-03-02 13:59:04'),
            array('category_id' => '1','question' => 'How Will Communication Take Place Between Us?','answer' => 'Maecenas tempus tellus eget condimentu oncussam mperngsu libero sit amet adipiscing ue sed ipsum. Nam quam nunc, blandit veluctus pulvinar. Maecenas tempus.','status' => '1','created_at' => '2023-03-02 13:59:21','updated_at' => '2023-03-02 13:59:21'),
            array('category_id' => '1','question' => 'Not Working Properly In Donation Page?','answer' => 'Maecenas tempus tellus eget condimentu oncussam mperngsu libero sit amet adipiscing ue sed ipsum. Nam quam nunc, blandit veluctus pulvinar. Maecenas tempus.','status' => '1','created_at' => '2023-03-02 13:59:49','updated_at' => '2023-03-02 13:59:49'),
            array('category_id' => '1','question' => 'How is charity fund collection?','answer' => 'Maecenas tempus tellus eget condimentu oncussam mperngsu libero sit amet adipiscing ue sed ipsum. Nam quam nunc, blandit veluctus pulvinar. Maecenas tempus.','status' => '1','created_at' => '2023-03-02 14:01:21','updated_at' => '2023-03-02 14:26:35')
          );
    }
}
