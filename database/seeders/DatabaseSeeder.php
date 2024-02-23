<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::create(
            [
                'name' => 'Sarah',
                'user_firstname' => 'Sarah',
                'user_lastname' => 'Pinna',
                'email' => 'sarah@befamily.it',
                'password' => 'Spinn@86',
                'user_birthdate' => '1986-08-4',
            ],
            [
                'name' => 'SarahC',
                'user_firstname' => 'Sarah',
                'user_lastname' => 'Pinna',
                'email' => 'sarah.pinna@icloud.com',
                'password' => 'Spinn@86!',
                'user_birthdate' => '1986-08-4',
            ]
        );
    }
}
