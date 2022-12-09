<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\User::factory(1)->create();

        // \App\Models\User::factory()->create([
        //     'userName' => 'admin',
        //     'email_verified_at' => now(),
        //     'firstName' => 'admin',
        //     'lastName' => 'admin',
        //     'email' => 'admin@admin.com',
        //     'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
        //     'isActive' => 1,
        // ]);
    }
}
