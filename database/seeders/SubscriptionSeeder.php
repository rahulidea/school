<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['name' => 'Freemium', 'price' => '0'],
            ['name' => 'Gold', 'price' => '999'],
            ['name' => 'Daimond', 'price' => '2999'],
        ];
        DB::table('subscriptions')->insert($data);
    }
}
