<?php

namespace App\Forum;

use App\Content\CompanyContentFile;
use App\Models\Company;

class ForumCatalog
{
    private const CONTENT_DIRECTORY = 'forum_threads';

    /**
     * @return list<array{slug: string, author: string, title: string, posts: list<array{author: string, body: string}>}>
     */
    public function threadsOf(Company $company): array
    {
        /** @var list<array{slug: string, author: string, title: string, posts: list<array{author: string, body: string}>}> */
        return CompanyContentFile::entries($company, self::CONTENT_DIRECTORY);
    }
}
