<?php

namespace Database\Factories;

use App\FloppyDisks\DiskSource;
use App\FloppyDisks\FloppyDiskKind;
use App\Models\FloppyDisk;
use App\Models\PlayerFloppyDisk;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PlayerFloppyDisk>
 */
class PlayerFloppyDiskFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'floppy_disk_id' => FloppyDisk::factory(),
            'order_id' => null,
            'source' => DiskSource::Shop,
            'label' => null,
            'is_write_protected' => false,
        ];
    }

    public function blank(): static
    {
        return $this->state(fn (): array => ['floppy_disk_id' => FloppyDisk::factory()->state(['kind' => FloppyDiskKind::Blank])]);
    }

    public function writeProtected(): static
    {
        return $this->state(fn (): array => ['is_write_protected' => true]);
    }
}
