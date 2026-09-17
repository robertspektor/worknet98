<?php

namespace App\Content;

use App\Models\Company;
use Illuminate\Support\Facades\File;
use SplFileInfo;

class CompanyContentFile
{
    /**
     * @return list<array<string, mixed>>
     */
    public static function entries(Company $company, string $directory): array
    {
        /** @var list<array<string, mixed>> */
        return self::read($company, $directory);
    }

    /**
     * @return array<string, mixed>
     */
    public static function data(Company $company, string $directory): array
    {
        /** @var array<string, mixed> */
        return self::read($company, $directory);
    }

    /**
     * @return list<string>
     */
    public static function companySlugs(string $directory): array
    {
        return array_values(array_map(
            fn (SplFileInfo $file): string => $file->getBasename('.json'),
            File::files(self::pathTo($directory)),
        ));
    }

    /**
     * @return array<mixed>
     */
    private static function read(Company $company, string $directory): array
    {
        $path = self::pathTo("{$directory}/{$company->slug}.json");

        return File::exists($path) ? File::json($path, JSON_THROW_ON_ERROR) : [];
    }

    private static function pathTo(string $relativePath): string
    {
        return database_path("content/{$relativePath}");
    }
}
