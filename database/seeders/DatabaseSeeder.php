<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@cookbook.com',
            'password' => Hash::make('admin'),
            'is_admin' => true,
            'remember_token' => Str::random(10),
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Test User1',
            'email' => 'test1@cookbook.com',
            'password' => Hash::make('test1234'),
            'is_admin' => false,
            'remember_token' => Str::random(10),
        ]);
        \App\Models\User::factory()->create([
            'name' => 'Test User2',
            'email' => 'test2@cookbook.com',
            'password' => Hash::make('test1234'),
            'is_admin' => false,
            'remember_token' => Str::random(10),
        ]);
    }
}
