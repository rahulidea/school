<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->delete();

        $data = [
            ['type' => 'current_session', 'school_id' => '2', 'description' => '2024-2025'],
            ['type' => 'system_title', 'school_id' => '2', 'description' => 'VNW'],
            ['type' => 'system_name', 'school_id' => '2', 'description' => 'Vidya Niketan West'],
            ['type' => 'term_ends', 'school_id' => '2', 'description' => '7/03/2025'],
            ['type' => 'term_begins', 'school_id' => '2', 'description' => '8/030/2024'],
            ['type' => 'phone', 'school_id' => '2', 'description' => '0123456789'],
            ['type' => 'address', 'school_id' => '2', 'description' => '18B North Central Park, Behind Central Square Tourist Center'],
            ['type' => 'system_email', 'school_id' => '2', 'description' => 'vnwacademy@vnw.com'],
            ['type' => 'alt_email', 'school_id' => '2', 'description' => ''],
            ['type' => 'email_host', 'school_id' => '2', 'description' => ''],
            ['type' => 'email_pass', 'school_id' => '2', 'description' => ''],
            ['type' => 'lock_exam', 'school_id' => '2', 'description' => 0],
            ['type' => 'logo', 'school_id' => '2', 'description' => ''],
            ['type' => 'next_term_fees_j', 'school_id' => '2', 'description' => '2000'],
            ['type' => 'next_term_fees_pn', 'school_id' => '2', 'description' => '2500'],
            ['type' => 'next_term_fees_p', 'school_id' => '2', 'description' => '2500'],
            ['type' => 'next_term_fees_n', 'school_id' => '2', 'description' => '2560'],
            ['type' => 'next_term_fees_s', 'school_id' => '2', 'description' => '1560'],
            ['type' => 'next_term_fees_c', 'school_id' => '2', 'description' => '160'],
        ];

        DB::table('settings')->insert($data);

    }
}
