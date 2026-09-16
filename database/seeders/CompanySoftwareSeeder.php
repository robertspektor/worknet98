<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Workplace\CompanySoftwareCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class CompanySoftwareSeeder extends Seeder
{
    public function run(CompanySoftwareCatalog $catalog): void
    {
        foreach ($catalog->companySlugs() as $slug) {
            $company = Company::query()->where('slug', $slug)->firstOrFail();

            /** @var array<string, mixed> $content */
            $content = $catalog->contentFor($company);

            $this->seedContacts($company, $content);
            $this->seedRecords($company, 'technicians', $content);
            $this->seedRecords($company, 'customers', $content);
        }
    }

    /**
     * @param  array<string, mixed>  $content
     */
    private function seedContacts(Company $company, array $content): void
    {
        $company->update(Arr::only($content, ['office_address', 'manager_name', 'manager_address']));
    }

    /**
     * @param  'technicians'|'customers'  $relation
     * @param  array<string, mixed>  $content
     */
    private function seedRecords(Company $company, string $relation, array $content): void
    {
        /** @var list<array<string, mixed>> $records */
        $records = $content[$relation];

        foreach ($records as $record) {
            $company->{$relation}()->updateOrCreate(['slug' => $record['slug']], Arr::except($record, ['slug']));
        }
    }
}
