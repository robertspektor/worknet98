<?php

namespace App\Career\Promotions;

use App\Career\Events\PlayerPromoted;
use App\Cases\Deadlines\CaseBooking;
use App\Cases\Deadlines\CaseHandover;
use App\Cases\WorkCaseKind;
use App\Game\ActionRefused;
use App\Mailbox\Mailbox;
use App\Models\Employment;
use App\Models\Position;
use App\Models\PromotionOffer;
use App\Models\PromotionOfferOption;
use App\Models\WorkCase;
use Illuminate\Support\Facades\DB;

class Promotion
{
    public function __construct(
        private readonly OpenPromotionOffer $openOffer,
        private readonly CaseBooking $booking,
        private readonly CaseHandover $handover,
        private readonly PromotionLetter $letter,
        private readonly Mailbox $mailbox,
    ) {}

    public function accept(PromotionOffer $offer, int $positionId): PromotionOffer
    {
        [$offer, $previousPosition] = DB::transaction(function () use ($offer, $positionId): array {
            $offer = $this->openOffer->lock($offer);
            $option = $offer->options->firstWhere('position_id', $positionId) ?? throw new ActionRefused(PromotionRefusal::PositionNotOffered);
            $this->lockVacant($option->position_id);
            $employment = $offer->employment;
            $previousPosition = $employment->position;

            $this->releaseUnbookedCases($employment);
            $this->moveInto($employment, $option);
            $offer->update(['status' => PromotionOfferStatus::Accepted, 'accepted_position_id' => $positionId, 'responded_at' => now()]);
            $this->mailbox->deliver($employment->user, $this->letter->compose($employment), $employment);

            return [$offer, $previousPosition];
        });

        PlayerPromoted::dispatch($offer->employment, $previousPosition);

        return $offer;
    }

    private function lockVacant(int $positionId): void
    {
        $position = Position::query()->whereKey($positionId)->lockForUpdate()->firstOrFail();

        if ($position->isHeldByPlayer()) {
            throw new ActionRefused(PromotionRefusal::PositionTaken);
        }
    }

    private function releaseUnbookedCases(Employment $employment): void
    {
        WorkCase::query()->whereBelongsTo($employment)->open()->where('kind', WorkCaseKind::Template)->with('branch.company')->get()
            ->reject(fn (WorkCase $workCase): bool => $this->booking->isBookedByAssignee($workCase))
            ->each(fn (WorkCase $workCase) => $this->handover->handOverToNpc($workCase));
    }

    private function moveInto(Employment $employment, PromotionOfferOption $option): void
    {
        $employment->update([
            'position_id' => $option->position_id,
            'daily_salary' => $option->daily_salary,
            'position_started_at' => now(),
        ]);
        $employment->unsetRelation('position');
    }
}
