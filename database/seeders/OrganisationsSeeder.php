<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganisationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('schools')->truncate();
        DB::table('organisations')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        
        $data = [
            ['name' => 'Vidya Niketan', 'subscription_id' => '1', 'expiry_date' => now()->addYear(),],
            ['name' => 'Sarvodaya Vidyalay', 'subscription_id' => '2', 'expiry_date' => now()->addMonth(),],
            ['name' => 'Shanti Niketan', 'subscription_id' => '3', 'expiry_date' => now()->addWeek(),],
            ['name' => 'Ratan Vidyalay', 'subscription_id' => '2', 'expiry_date' => now()->addYear(),],
            ['name' => 'Krishna Vidya Mandir', 'subscription_id' => '1', 'expiry_date' => now()->addYear(),],
        ];
        DB::table('organisations')->insert($data);

        $data = [
            ['name' => 'Vidya Niketan East', 'organisation_id' => '1'],
            ['name' => 'Vidya Niketan West', 'organisation_id' => '2'],
            ['name' => 'Sarvodaya Vidyalay Delhi', 'organisation_id' => '2'],
            ['name' => 'Sarvodaya Vidyalay Mumbai', 'organisation_id' => '2'],
            ['name' => 'Sarvodaya Vidyalay Varanasi', 'organisation_id' => '2'],
            ['name' => 'Shanti Niketan', 'organisation_id' => '3'],
            ['name' => 'Ratan Vidyalay', 'organisation_id' => '4'],
            ['name' => 'Krishna Vidya Mandir Luxa', 'organisation_id' => '5'],
            ['name' => 'Krishna Vidya Mandir Durgakund', 'organisation_id' => '5'],
        ];
        DB::table('schools')->insert($data);
    }
}
