<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

class CompanySeeder extends Seeder
{
    private const CONTENT_PATH = 'content/companies.json';

    public function run(): void
    {
        foreach ($this->companies() as $data) {
            $this->seedCompany($data);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function seedCompany(array $data): void
    {
        $company = Company::updateOrCreate(['slug' => $data['slug']], Arr::except($data, ['slug', 'job_openings']));

        /** @var list<array<string, mixed>> $openings */
        $openings = $data['job_openings'];

        foreach ($openings as $opening) {
            $company->jobOpenings()->updateOrCreate(['slug' => $opening['slug']], Arr::except($opening, ['slug']));
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function companies(): array
    {
        /** @var list<array<string, mixed>> */
        return File::json(database_path(self::CONTENT_PATH), JSON_THROW_ON_ERROR);
    }
}
