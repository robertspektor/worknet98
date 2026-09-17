<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([CompanySeeder::class, CitySeeder::class, PopulationSeeder::class, BranchSeeder::class, ForumSeeder::class, FloppyDiskSeeder::class, HardwarePartSeeder::class]);

        User::factory()->create(['email' => 'player@desklife98.test']);
    }
}
