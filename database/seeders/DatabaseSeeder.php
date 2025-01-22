<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Database\Seeders\User\UserSeeder;
use Database\Seeders\Admin\RoleSeeder;
use Database\Seeders\Admin\AdminSeeder;
use Database\Seeders\CategoryTypeSeeder;
use Database\Seeders\Admin\SetupKycSeeder;
use Database\Seeders\Admin\SetupSeoSeeder;
use Database\Seeders\Admin\ExtensionSeeder;
use Database\Seeders\Admin\SetupPageSeeder;
use Database\Seeders\Demo\SetupEmailSeeder;
use Database\Seeders\Admin\AppSettingsSeeder;
use Database\Seeders\Admin\AdminHasRoleSeeder;
use Database\Seeders\Admin\SiteSectionsSeeder;
use Database\Seeders\Admin\BasicSettingsSeeder;
// Demo Seeder
use Database\Seeders\Demo\PaymentGateWaySeeder;
use Database\Seeders\Admin\SystemMaintenanceSeeder;
use Database\Seeders\Admin\TransactionSettingSeeder;
// Fresh Seeder
use Database\Seeders\Demo\User\UserSeeder as DemoUserSeeder;
use Database\Seeders\Demo\BasicSettingsSeeder as DemoBasicSettingsSeeder;
use Database\Seeders\Fresh\Admin\ExtensionSeeder as FreshExtensionSeeder;
use Database\Seeders\Fresh\Admin\SetupEmailSeeder as FreshSetupEmailSeeder;
use Database\Seeders\Fresh\Admin\PaymentGateWaySeeder as FreshPaymentGateWaySeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Demo Project Seeder

        $this->call([
            DemoUserSeeder::class,
            AdminSeeder::class,
            RoleSeeder::class,
            AdminHasRoleSeeder::class,
            CurrencySeeder::class,
            DemoBasicSettingsSeeder::class,
            CategoryTypeSeeder::class,
            CampaignSeeder::class,
            EventSeeder::class,
            SiteSectionsSeeder::class,
            SetupSeoSeeder::class,
            AppSettingsSeeder::class,
            LanguageSeeder::class,
            SetupEmailSeeder::class,
            ExtensionSeeder::class,
            SetupPageSeeder::class,
            PaymentGateWaySeeder::class,
            SystemMaintenanceSeeder::class,
        ]);


        // Fresh Project Seeder

        // $this->call([
        //     AdminSeeder::class,
        //     RoleSeeder::class,
        //     AdminHasRoleSeeder::class,
        //     CurrencySeeder::class,
        //     BasicSettingsSeeder::class,
        //     CategoryTypeSeeder::class,
        //     CampaignSeeder::class,
        //     EventSeeder::class,
        //     SiteSectionsSeeder::class,
        //     SetupSeoSeeder::class,
        //     AppSettingsSeeder::class,
        //     LanguageSeeder::class,
        //     FreshSetupEmailSeeder::class,
        //     FreshExtensionSeeder::class,
        //     SetupPageSeeder::class,
        //     FreshPaymentGateWaySeeder::class,
        //     SystemMaintenanceSeeder::class
        // ]);
    }
}
