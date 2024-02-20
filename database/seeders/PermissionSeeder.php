<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::update('SET FOREIGN_KEY_CHECKS = ?', [0]);
        DB::update('TRUNCATE TABLE permissions');
        DB::update('TRUNCATE TABLE role_has_permissions');
        DB::update('TRUNCATE TABLE model_has_permissions');
        DB::update('SET FOREIGN_KEY_CHECKS = ?', [1]);

        // All permissions in our APP that SuperUser gets - DEFAULT bootstrap
        $models = [
            'users' => ['list', 'view', 'create', 'update', 'delete'],
            'roles' => ['list', 'view', 'create', 'update', 'delete'],
            'permissions' => ['list', 'view', 'create', 'update', 'delete'],
            'blogs' => ['list', 'view', 'create', 'update', 'delete', 'restore', 'destroy'],
            'writers' => ['list', 'view', 'create', 'update', 'delete', 'restore', 'destroy'],
            'tags' => ['list', 'view', 'create', 'update', 'delete', 'restore', 'destroy']
        ];

        $adminPermissions = [
            'users' => ['list', 'view', 'create', 'update'],
            'roles' => ['list', 'view'],
            'permissions' => ['list', 'view'],
            'blogs' => ['list', 'view', 'create', 'update', 'delete', 'restore'],
            'writers' => ['list', 'view', 'create', 'update', 'delete', 'restore'],
            'tags' => ['list', 'view', 'create', 'update', 'delete', 'restore', 'destroy']
        ];

        $webmasterPermissions = [
            'users' => ['list', 'view'],
            'blogs' => ['list', 'view', 'create', 'update'],
            'writers' => ['list', 'view', 'update'],
            'tags' => ['list', 'view', 'create', 'update', 'delete', 'restore', 'destroy']
        ];

        // SuperUser aka our APP
        foreach ($models as $model => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create(['name' => $model.'.'.$permission]);
            }
        }

        $adminRole = Role::where('name', 'Admin')->first();
            foreach ($adminPermissions as $model => $permissions) {
            foreach ($permissions as $permission) {
                $adminRole->givePermissionTo($model.'.'.$permission);
            }
        }

        $webmasterRole = Role::where('name', 'Webmaster')->first();
            foreach ($webmasterPermissions as $model => $permissions) {
            foreach ($permissions as $permission) {
                $webmasterRole->givePermissionTo($model.'.'.$permission);
            }
        }
    }
}
