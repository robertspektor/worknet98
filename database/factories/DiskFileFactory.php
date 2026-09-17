<?php

namespace Database\Factories;

use App\FloppyDisks\DiskFileKind;
use App\Models\DiskFile;
use App\Models\PlayerFloppyDisk;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DiskFile>
 */
class DiskFileFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $body = fake()->sentence();

        return [
            'player_floppy_disk_id' => PlayerFloppyDisk::factory(),
            'name' => mb_strtoupper(fake()->unique()->lexify('????????')).'.TXT',
            'kind' => DiskFileKind::Text,
            'body' => $body,
            'content_key' => null,
            'program' => null,
            'size_bytes' => strlen($body),
        ];
    }

    public function setup(string $program, int $sizeBytes = 300_000): static
    {
        return $this->state(fn (): array => [
            'name' => 'SETUP.EXE',
            'kind' => DiskFileKind::Setup,
            'body' => null,
            'program' => $program,
            'size_bytes' => $sizeBytes,
        ]);
    }
}
