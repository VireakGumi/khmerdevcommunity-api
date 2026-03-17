<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Passport\ClientRepository;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CommunitySeeder::class,
        ]);

        try {
            app(ClientRepository::class)->personalAccessClient('users');
        } catch (RuntimeException) {
            app(ClientRepository::class)->createPersonalAccessGrantClient('Khmer Dev Community Personal Access Client', 'users');
        }
    }
}
