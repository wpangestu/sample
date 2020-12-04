<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;

class UserDefaultSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('123456'),
            'userid' => Str::random(6),
            'phone' => '08'.mt_rand(1000000000, 9999999999)
        ]);

        $user->assignRole('admin');
        
        $user = User::create([
            'name' => 'user',
            'email' => 'user@user.com',
            'password' => bcrypt('123456'),
            'userid' => Str::random(6),
            'phone' => '08'.mt_rand(1000000000, 9999999999)
        ]);

        $user->assignRole('user');

        $user = User::create([
            'name' => 'teknisi',
            'email' => 'teknisi@teknisi.com',
            'password' => bcrypt('123456'),
            'userid' => Str::random(6),
            'phone' => '08'.mt_rand(1000000000, 9999999999)
        ]);

        $user->assignRole('teknisi');


    }
}
