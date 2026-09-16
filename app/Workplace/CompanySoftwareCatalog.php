<?php

namespace App\Workplace;

use App\Models\Company;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class CompanySoftwareCatalog
{
    private const CONTENT_DIRECTORY = 'content/company_software';

    /**
     * @return array<string, mixed>|null
     */
    public function contentFor(Company $company): ?array
    {
        $path = database_path(self::CONTENT_DIRECTORY."/{$company->slug}.json");

        /** @var array<string, mixed>|null */
        return File::exists($path) ? File::json($path, JSON_THROW_ON_ERROR) : null;
    }

    /**
     * @return array<string, string>
     */
    public function appNamesFor(Company $company): array
    {
        /** @var array<string, string> */
        return $this->contentFor($company)['app_names'] ?? [];
    }

    /**
     * @return list<string>
     */
    public function companySlugs(): array
    {
        return array_values(array_map(
            fn (SplFileInfo $file): string => $file->getBasename('.json'),
            File::files(database_path(self::CONTENT_DIRECTORY)),
        ));
    }
}
