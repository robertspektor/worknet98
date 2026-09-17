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
        $this->call([CompanySeeder::class, BranchSeeder::class, FloppyDiskSeeder::class]);

        User::factory()->create(['email' => 'player@desklife98.test']);
    }
}
