<?php

namespace Database\Seeders;

use App\Models\HardwarePart;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class HardwarePartSeeder extends Seeder
{
    private const CONTENT_PATH = 'content/hardware_parts.json';

    public function run(): void
    {
        foreach ($this->hardwareParts() as $data) {
            HardwarePart::updateOrCreate(['slug' => $data['slug']], Arr::except($data, ['slug']));
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function hardwareParts(): array
    {
        /** @var list<array<string, mixed>> */
        return File::json(database_path(self::CONTENT_PATH), JSON_THROW_ON_ERROR);
    }
}
