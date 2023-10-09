<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\fceUser;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        fceUser::create(
            [
                'user_firstname' => 'Sarah',
                'user_lastname' => 'Pinna',
                'email' => 'sarah.pinna@icloud.com',
                'user_created_at' => now(),
                'password' => 'Spinn@86!',
                'user_birthdate' => '1986-08-4',
            ]
        );
    }
}
