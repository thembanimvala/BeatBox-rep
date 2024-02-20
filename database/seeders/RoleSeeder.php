<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        {
            DB::update('SET FOREIGN_KEY_CHECKS = ?', [0]);
            DB::update('TRUNCATE TABLE roles');
            DB::update('SET FOREIGN_KEY_CHECKS = ?', [1]);
            $role = ['Superuser', 'Admin', 'Webmaster'];
            foreach ($role as $role) {
                Role::firstOrCreate(['name' => $role]);
            }
        }
    }
}
