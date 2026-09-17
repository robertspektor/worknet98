<?php

namespace App\Organization;

use App\Models\Company;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class BranchCatalog
{
    private const CONTENT_DIRECTORY = 'content/branches';

    /**
     * @return list<array<string, mixed>>
     */
    public function branchesOf(Company $company): array
    {
        $path = database_path(self::CONTENT_DIRECTORY."/{$company->slug}.json");

        /** @var list<array<string, mixed>> */
        return File::exists($path) ? File::json($path, JSON_THROW_ON_ERROR) : [];
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
