<?php

namespace Database\Seeders;
use App\Models\Achievement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $admin = User::factory()->create([
            'name' => 'Admin',
            'surname' => '',
            'lastname' => '',
            'date_of_birth' => '2001-10-08',
            'email' => 'admin@nnuace.ru',
            'password' => Hash::make('ADM!N420420'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $employee = User::factory()->create([
            'name' => 'User',
            'surname' => '',
            'lastname' => '',
            'date_of_birth' => '2001-10-08',
            'email' => 'user@example.ru',
            'password' => Hash::make('123123123'),
            'email_verified_at' => now(),
        ]);

        $employeeAchievement = Achievement::create([
            'title' => 'Employee',
            'subtitle' => 'Start work',
            'image_url' => 'https://w0.peakpx.com/wallpaper/147/254/HD-wallpaper-deadly-skull-rock-sultan-roll-skeletons-metal-skulls-symbols-scary-skeleton.jpg',
        ]);

        $adminAchievement = Achievement::create([
            'title' => 'Boss guy',
            'subtitle' => 'Start administrating',
            'image_url' => 'https://w0.peakpx.com/wallpaper/147/254/HD-wallpaper-deadly-skull-rock-sultan-roll-skeletons-metal-skulls-symbols-scary-skeleton.jpg',
        ]);

        $employee->achievements()->attach($employeeAchievement->id);
        $admin->achievements()->attach($adminAchievement->id);
    }
}
