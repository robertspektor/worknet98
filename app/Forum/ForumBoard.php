<?php

namespace App\Forum;

use App\Models\Company;
use App\Models\ForumPost;
use App\Models\ForumThread;
use Illuminate\Database\Eloquent\Collection;

class ForumBoard
{
    /**
     * @return Collection<int, ForumThread>
     */
    public function threadsOf(Company $company): Collection
    {
        return ForumThread::query()
            ->whereBelongsTo($company)
            ->withCount('posts')
            ->with(['authorPosition.person'])
            ->latest('last_posted_at')
            ->get();
    }

    /**
     * @return Collection<int, ForumPost>
     */
    public function postsOf(ForumThread $thread): Collection
    {
        return $thread->posts()
            ->with(['authorPosition.person', 'authorEmployment'])
            ->oldest('id')
            ->get();
    }
}
