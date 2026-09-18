<?php

namespace App\Living;

use App\Game\GameClock;
use App\Mailbox\EmailDraft;
use App\Mailbox\Mailbox;
use App\Models\LedgerEntry;
use App\Models\LivingCostBill;
use App\Models\User;
use App\Work\LedgerReason;
use Carbon\CarbonImmutable;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;

class MonthlyBills
{
    public function __construct(
        private readonly GameClock $clock,
        private readonly LivingCostCatalog $catalog,
        private readonly Mailbox $mailbox,
    ) {}

    public function chargeDue(): int
    {
        $period = $this->clock->today()->startOfMonth()->subMonth();
        $charged = 0;

        User::query()
            ->whereDoesntHave('livingCostBills', fn ($bills) => $bills->where('period', $period->format('Y-m')))
            ->where('created_at', '<', $this->clock->toReal($period->endOfMonth()))
            ->lazyById()
            ->each(function (User $player) use ($period, &$charged): void {
                if ($this->charge($player, $period)) {
                    $charged++;
                }
            });

        return $charged;
    }

    private function charge(User $player, CarbonImmutable $period): bool
    {
        $costs = $this->catalog->forLocale($player->locale);

        if ($costs === [] || ! $this->wasSeenIn($player, $period)) {
            return false;
        }

        try {
            DB::transaction(function () use ($player, $period, $costs): void {
                LivingCostBill::create([
                    'user_id' => $player->id,
                    'period' => $period->format('Y-m'),
                    'amount' => array_sum(array_map(fn (LivingCost $cost): int => $cost->amount, $costs)),
                ]);

                foreach ($costs as $cost) {
                    LedgerEntry::create(['user_id' => $player->id, 'amount' => -$cost->amount, 'reason' => LedgerReason::LivingCosts]);
                    $this->mailbox->deliver($player, $this->bill($cost, $period, $player->locale));
                }
            });
        } catch (UniqueConstraintViolationException) {
            return false;
        }

        return true;
    }

    private function wasSeenIn(User $player, CarbonImmutable $period): bool
    {
        return $player->last_seen_at !== null && $player->last_seen_at->gte($this->clock->toReal($period));
    }

    private function bill(LivingCost $cost, CarbonImmutable $period, string $locale): EmailDraft
    {
        $replacements = [
            'month' => $period->settings(['locale' => $locale])->translatedFormat('F Y'),
            'amount' => $cost->amount,
            'sender' => $cost->senderName,
        ];

        return new EmailDraft(
            senderName: $cost->senderName,
            senderAddress: $cost->senderAddress,
            subject: __("game_mail.bill.{$cost->kind}.subject", $replacements, $locale),
            body: __("game_mail.bill.{$cost->kind}.body", $replacements, $locale),
        );
    }
}
