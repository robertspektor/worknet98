<?php

namespace Database\Factories;

use App\Models\FloppyDisk;
use App\Models\InstalledProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InstalledProgram>
 */
class InstalledProgramFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'floppy_disk_id' => FloppyDisk::factory()->program('minefield'),
            'program' => 'minefield',
            'installed_at' => now(),
        ];
    }
}
