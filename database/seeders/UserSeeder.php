<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = User::updateOrCreate(
            [
                'email'               => 'thembani@beatbox.co.za',],
            [
                'name'                => 'Thembani',
                'password'            => bcrypt('password'),
                'email_verified_at'   => Carbon::now(),
                'is_active'           => TRUE,
            ]
        );
        $user2 = User::firstOrCreate(['email'               => 'admin@beatbox.co.za',],
            ['name'                => 'Admin',
            'password'            => bcrypt('password'),
            'email_verified_at'   => Carbon::now(),
            'is_active'           => TRUE,
        ]);

        $user3 = User::firstOrCreate(['email'               => 'webmaster@beatbox.co.za',],
        [
            'name'                => 'Webmaster',
            'password'            => bcrypt('password'),
            'email_verified_at'   => Carbon::now(),
            'is_active'           => TRUE,
        ]);

        // then add the role to the user
        $user1->assignRole(User::SUPERUSER);
        $user2->assignRole(User::ADMIN);
        $user3->assignRole(User::WEBMASTER);

        User::factory()->count(10)->create();
    }
}
