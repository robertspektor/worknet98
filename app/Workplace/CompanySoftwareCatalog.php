<?php

namespace App\Workplace;

use App\Models\Company;
use Illuminate\Support\Facades\File;

class CompanySoftwareCatalog
{
    private const CONTENT_DIRECTORY = 'content/company_software';

    /**
     * @return array<string, string>
     */
    public function appNamesFor(Company $company): array
    {
        $path = database_path(self::CONTENT_DIRECTORY."/{$company->slug}.json");

        /** @var array<string, string> */
        return File::exists($path) ? File::json($path, JSON_THROW_ON_ERROR)['app_names'] : [];
    }
}
