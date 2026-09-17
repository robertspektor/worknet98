<?php

namespace App\Forum;

use App\Game\ActionRefused;
use App\Models\Company;
use App\Models\Employment;
use App\Models\ForumPost;
use App\Models\ForumThread;
use App\Models\Position;
use App\Models\User;
use App\Work\ShiftRefusal;
use Illuminate\Support\Facades\DB;

class ForumWriter
{
    private const SECONDS_BETWEEN_POSTS = 10;

    public function startThread(User $player, string $title, string $body): ForumThread
    {
        $employment = $this->employmentOf($player);
        $this->ensureNotTooFast($employment);

        return DB::transaction(function () use ($employment, $title, $body): ForumThread {
            $thread = ForumThread::create([
                'company_id' => $employment->company_id,
                'author_position_id' => $employment->position_id,
                'author_employment_id' => $employment->id,
                'title' => $title,
                'last_posted_at' => now(),
            ]);

            $this->write($thread, $employment, $body);

            return $thread;
        });
    }

    public function announce(Company $company, Position $author, string $title, string $body): ForumThread
    {
        return DB::transaction(function () use ($company, $author, $title, $body): ForumThread {
            $thread = ForumThread::create([
                'company_id' => $company->id,
                'author_position_id' => $author->id,
                'title' => $title,
                'last_posted_at' => now(),
            ]);

            ForumPost::create([
                'forum_thread_id' => $thread->id,
                'author_position_id' => $author->id,
                'body' => $body,
            ]);

            return $thread;
        });
    }

    public function reply(User $player, ForumThread $thread, string $body): ForumPost
    {
        $employment = $this->employmentOf($player);
        $this->ensureNotTooFast($employment);

        return DB::transaction(fn (): ForumPost => $this->write($thread, $employment, $body));
    }

    private function write(ForumThread $thread, Employment $employment, string $body): ForumPost
    {
        $thread->update(['last_posted_at' => now()]);

        return ForumPost::create([
            'forum_thread_id' => $thread->id,
            'author_position_id' => $employment->position_id,
            'author_employment_id' => $employment->id,
            'body' => $body,
        ]);
    }

    private function ensureNotTooFast(Employment $employment): void
    {
        $isTooFast = ForumPost::query()
            ->where('author_employment_id', $employment->id)
            ->where('created_at', '>', now()->subSeconds(self::SECONDS_BETWEEN_POSTS))
            ->exists();

        if ($isTooFast) {
            throw new ActionRefused(ForumRefusal::TooFast);
        }
    }

    private function employmentOf(User $player): Employment
    {
        return $player->employment ?? throw new ActionRefused(ShiftRefusal::NotEmployed);
    }
}
