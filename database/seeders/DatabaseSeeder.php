<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the single administrator account when credentials are supplied.
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->command->warn('ADMIN_EMAIL and ADMIN_PASSWORD are not set; skipping the administrator account.');

            return;
        }

        User::query()->updateOrCreate(
            ['email' => $email],
            ['name' => env('ADMIN_NAME', 'Administrator'), 'password' => $password],
        );
    }
}
