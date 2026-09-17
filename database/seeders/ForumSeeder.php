<?php

namespace Database\Seeders;

use App\Forum\ForumCatalog;
use App\Models\Company;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\Position;
use Illuminate\Database\Seeder;

class ForumSeeder extends Seeder
{
    public function run(ForumCatalog $catalog): void
    {
        foreach (Company::with('branches')->get() as $company) {
            foreach ($catalog->threadsOf($company) as $content) {
                $this->seedThread($company, $content);
            }
        }
    }

    /**
     * @param  array{slug: string, author: string, title: string, posts: list<array{author: string, body: string}>}  $content
     */
    private function seedThread(Company $company, array $content): void
    {
        $thread = ForumThread::query()->updateOrCreate(
            ['company_id' => $company->id, 'slug' => $content['slug']],
            [
                'author_position_id' => $this->positionId($company, $content['author']),
                'title' => $content['title'],
                'last_posted_at' => now(),
            ],
        );

        if ($thread->posts()->exists()) {
            return;
        }

        foreach ($content['posts'] as $post) {
            ForumPost::create([
                'forum_thread_id' => $thread->id,
                'author_position_id' => $this->positionId($company, $post['author']),
                'body' => $post['body'],
            ]);
        }
    }

    private function positionId(Company $company, string $slug): int
    {
        return Position::query()
            ->whereRelation('branch', 'company_id', $company->id)
            ->where('slug', $slug)
            ->firstOrFail()
            ->id;
    }
}
