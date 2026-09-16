<?php

namespace App\Notepad;

use App\Models\Note;
use App\Models\User;

class NoteBook
{
    public function noteOf(User $player): string
    {
        return Note::query()->where('user_id', $player->id)->value('body') ?? '';
    }

    public function write(User $player, string $body): void
    {
        Note::updateOrCreate(['user_id' => $player->id], ['body' => $body]);
    }
}
