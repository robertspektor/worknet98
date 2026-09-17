<?php

namespace App\Career;

use App\Cases\Deadlines\CaseHandover;
use App\Mailbox\Mailbox;
use App\Models\Employment;
use App\Models\WorkCase;
use Illuminate\Support\Facades\DB;

class Dismissal
{
    public function __construct(
        private readonly CaseHandover $handover,
        private readonly DismissalLetter $letter,
        private readonly Mailbox $mailbox,
    ) {}

    public function dismiss(Employment $employment): void
    {
        DB::transaction(function () use ($employment): void {
            $employment->update(['ended_at' => now()]);

            WorkCase::query()->whereBelongsTo($employment)->open()->get()
                ->each(fn (WorkCase $workCase) => $this->handover->handOverToNpc($workCase));

            $this->mailbox->deliver($employment->user, $this->letter->compose($employment));
        });
    }
}
