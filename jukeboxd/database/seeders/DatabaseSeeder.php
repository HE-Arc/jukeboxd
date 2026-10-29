<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Créer un utilisateur de test via la Factory
        if (!User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'username' => 'testuser',
                'email' => 'test@example.com',
                'password' => 'password123',
            ]);
        }

        // User::factory(5)->create();

        $this->call([
            AlbumSeeder::class,
        ]);
    }
}
