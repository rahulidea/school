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
        $data = [
            ['name' => 'Vidya Niketan', 'subscription_id' => '1', 'expiry_date' => now()->addYear(),],
            ['name' => 'Sarvodaya Vidyalay', 'subscription_id' => '2', 'expiry_date' => now()->addMonth(),],
            ['name' => 'Shanti Niketan', 'subscription_id' => '3', 'expiry_date' => now()->addWeek(),],
            ['name' => 'Ratan Vidyalay', 'subscription_id' => '2', 'expiry_date' => now()->addYear(),],
            ['name' => 'Krishna Vidya Mandir', 'subscription_id' => '1', 'expiry_date' => now()->addYear(),],
        ];
        DB::table('organisations')->insert($data);
    }
}
