<?php

namespace Database\Seeders;

use App\Models\FloppyDisk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class FloppyDiskSeeder extends Seeder
{
    private const CONTENT_PATH = 'content/floppy_disks.json';

    public function run(): void
    {
        foreach ($this->floppyDisks() as $data) {
            FloppyDisk::updateOrCreate(['slug' => $data['slug']], ['pack_size' => 1, ...Arr::except($data, ['slug'])]);
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function floppyDisks(): array
    {
        /** @var list<array<string, mixed>> */
        return File::json(database_path(self::CONTENT_PATH), JSON_THROW_ON_ERROR);
    }
}
