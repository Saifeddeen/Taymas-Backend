<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@taymas.com',
            'password' => Hash::make("Taymas_superadmin@799"),
        ]);

        // User::factory()->create([
        //     'name' => 'Saifeddin S D Obaid',
        //     'email' => 'sdsdseao99@hotmail.com',
        //     'password' => Hash::make("Taymas_superadmin@saifeddin.799"),
        // ]);
    }
}
