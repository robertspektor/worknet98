<?php

namespace App\Mailbox;

use App\Models\Email;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class MailboxQuery
{
    /**
     * @return Collection<int, Email>
     */
    public function emailsOf(User $player, MailboxScope $scope): Collection
    {
        $employment = $player->employment;

        if ($scope === MailboxScope::Work && $employment === null) {
            return new Collection;
        }

        return $player->emails()
            ->when(
                $scope === MailboxScope::Work,
                fn ($query) => $query->where('employment_id', $employment?->id),
                fn ($query) => $query->whereNull('employment_id'),
            )
            ->latest('received_at')
            ->latest('id')
            ->get();
    }
}
