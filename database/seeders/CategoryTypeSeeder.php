<?php

namespace Database\Seeders;

use App\Models\CategoryType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category_types = array(
            array('id' => '1','name' => 'Charity','data' => '{"language":{"en":{"name":"Charity"},"es":{"name":"Caridad"},"ar":{"name":"صدقة"},"fr":{"name":"Charité"},"hi":{"name":"दान"}}}','slug' => 'faq-charity','type' => '1','status' => '1','created_at' => '2023-03-02 10:48:59','updated_at' => '2024-03-19 11:54:25'),
            array('id' => '2','name' => 'Donation','data' => '{"language":{"en":{"name":"Donation"},"es":{"name":"Donación"},"ar":{"name":"هبة"},"fr":{"name":"Donation"},"hi":{"name":"दान"}}}','slug' => 'faq-donation','type' => '1','status' => '1','created_at' => '2023-03-02 10:57:52','updated_at' => '2024-03-19 11:54:25'),
            array('id' => '3','name' => 'Medical & Aid Kit','data' => '{"language":{"en":{"name":"Medical & Aid Kit"},"es":{"name":"Botiquín médico y de ayuda"},"ar":{"name":"طقم طبي ومساعدات"},"fr":{"name":"Trousse médicale et d\'aide"},"hi":{"name":"चिकित्सा एवं सहायता किट"}}}','slug' => 'faq-medical-aid-kit','type' => '1','status' => '1','created_at' => '2023-03-02 10:58:30','updated_at' => '2024-11-08 05:18:05'),
            array('id' => '4','name' => 'Volenteer Team','data' => '{"language":{"en":{"name":"Volenteer Team"},"es":{"name":"Equipo de voluntarios"},"ar":{"name":"\\u0627\\u0644\\u0641\\u0631\\u064a\\u0642 \\u0627\\u0644\\u062a\\u0637\\u0648\\u0639\\u064a"},"fr":{"name":"\\u00c9quipe de b\\u00e9n\\u00e9voles"},"hi":{"name":"\\u0938\\u094d\\u0935\\u092f\\u0902\\u0938\\u0947\\u0935\\u0940 \\u091f\\u0940\\u092e"}}}','slug' => 'faq-volenteer-team','type' => '1','status' => '1','created_at' => '2023-03-02 10:58:41','updated_at' => '2024-11-08 05:21:09'),
            array('id' => '5','name' => 'Food and Water','data' => '{"language":{"en":{"name":"Food and Water"},"es":{"name":"Comida y agua"},"ar":{"name":"الغذاء والماء"},"fr":{"name":"Nourriture et eau"},"hi":{"name":"भोजन और पानी"}}}','slug' => 'faq-food-and-water','type' => '1','status' => '1','created_at' => '2023-03-02 10:58:51','updated_at' => '2024-11-08 05:22:30'),
            array('id' => '6','name' => 'Help Hoomeless People','data' => '{"language":{"en":{"name":"Help Hoomeless People"},"es":{"name":"Ayudar a las personas sin hogar"},"ar":{"name":"مساعدة المشردين"},"fr":{"name":"Aider les sans-abri"},"hi":{"name":"बेघर लोगों की मदद करें"}}}','slug' => 'faq-help-hoomeless-people','type' => '1','status' => '1','created_at' => '2023-03-02 10:58:58','updated_at' => '2024-11-08 05:21:56'),
            array('id' => '7','name' => 'Charity','data' => '{"language":{"en":{"name":"Charity"},"es":{"name":"Caridad"},"ar":{"name":"صدقة"},"fr":{"name":"Charité"},"hi":{"name":"दान"}}}','slug' => 'event-charity','type' => '2','status' => '1','created_at' => '2023-03-02 11:35:07','updated_at' => '2024-11-08 05:21:37'),
            array('id' => '8','name' => 'Donation','data' => '{"language":{"en":{"name":"Donation"},"es":{"name":"Donación"},"ar":{"name":"هبة"},"fr":{"name":"Donation"},"hi":{"name":"दान"}}}','slug' => 'event-donation','type' => '2','status' => '1','created_at' => '2023-03-02 11:35:22','updated_at' => '2024-11-08 05:21:31'),
            array('id' => '9','name' => 'Medical & Aid Kit','data' => '{"language":{"en":{"name":"Medical & Aid Kit"},"es":{"name":"Botiquín médico y de ayuda"},"ar":{"name":"طقم طبي ومساعدات"},"fr":{"name":"Trousse médicale et d\'aide"},"hi":{"name":"चिकित्सा एवं सहायता किट"}}}','slug' => 'event-medical-aid-kit','type' => '2','status' => '1','created_at' => '2023-03-02 11:35:39','updated_at' => '2024-03-21 05:15:11'),
            array('id' => '10','name' => 'Volenteer Team','data' => '{"language":{"en":{"name":"Volenteer Team"},"es":{"name":"Equipo de voluntarios"},"ar":{"name":"الفريق التطوعي"},"fr":{"name":"Équipe de bénévoles"},"hi":{"name":"स्वयंसेवी टीम"}}}','slug' => 'event-volenteer-team','type' => '2','status' => '1','created_at' => '2023-03-02 11:35:53','updated_at' => '2024-11-08 05:19:55'),
            array('id' => '11','name' => 'Food and Water','data' => '{"language":{"en":{"name":"Food and Water"},"es":{"name":"Comida y agua"},"ar":{"name":"الغذاء والماء"},"fr":{"name":"Nourriture et eau"},"hi":{"name":"भोजन और पानी"}}}','slug' => 'event-food-and-water','type' => '2','status' => '1','created_at' => '2023-03-02 11:36:06','updated_at' => '2024-11-08 05:22:23'),
            array('id' => '12','name' => 'Help Hoomeless People','data' => '{"language":{"en":{"name":"Help Hoomeless People"},"es":{"name":"Ayudar a las personas sin hogar"},"ar":{"name":"مساعدة المشردين"},"fr":{"name":"Aider les sans-abri"},"hi":{"name":"बेघर लोगों की मदद करें"}}}','slug' => 'event-help-hoomeless-people','type' => '2','status' => '1','created_at' => '2023-03-02 11:36:32','updated_at' => '2024-03-21 05:16:42')
        );

        CategoryType::insert($category_types);
    }
}
