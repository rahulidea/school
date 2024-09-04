<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // user_type id 1-5 | 5 for superadmin, 4 for admin ...
        DB::table('menus')->insert([
            [
                'name' => 'Dashboard',
                'url' => '/dashboard',
                'parent_id' => null,
                'icon' => 'dashboard-icon',
                'user_type_ids' => json_encode([5, 4]),
            ],
            [
                'name' => 'Students',
                'url' => '#',
                'parent_id' => null,
                'icon' => 'student-icon',
                'user_type_ids' => json_encode([5, 4, 3]),
            ],
                [
                    'name' => 'Admit Students',
                    'url' => '/students/create',
                    'parent_id' => 2,
                    'icon' => 'student-icon',
                    'user_type_ids' => json_encode([5, 4, 3, 1]),
                ],
                [
                    'name' => 'Student Information',
                    'url' => '#',
                    'parent_id' => 2,
                    'icon' => 'student-icon',
                    'user_type_ids' => json_encode([5, 4, 3, 1]),
                ],
                    [
                        'name' => 'Nursery 1',
                        'url' => '/students/list/1',
                        'parent_id' => 4,
                        'icon' => 'student-icon',
                        'user_type_ids' => json_encode([5, 4, 3, 1]),
                    ],
                    [
                        'name' => 'Nursery 2',
                        'url' => '/students/list/2',
                        'parent_id' => 4,
                        'icon' => 'student-icon',
                        'user_type_ids' => json_encode([5, 4, 3, 1]),
                    ],
            [
                'name' => 'Settings',
                'url' => '/settings',
                'parent_id' => null,
                'icon' => 'settings-icon',
                'user_type_ids' => json_encode([5]), // Example user types
            ],
            // Add more menu items as needed
        ]);
    }
}
