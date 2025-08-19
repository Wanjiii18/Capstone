<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Call all seeders in the correct order
        $this->call([
            AdminUserSeeder::class,
            DefaultUserSeeder::class,
            SampleDataSeeder::class,
            KarenderiaDataSeeder::class,
        ]);

        $this->command->info('All seeders have been executed successfully!');
    }
}
