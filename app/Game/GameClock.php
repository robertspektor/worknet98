<?php

namespace App\Game;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;

class GameClock
{
    private const DISPLAY_FORMAT = 'Y-m-d\TH:i:s';

    public function now(): CarbonImmutable
    {
        return $this->fromReal(CarbonImmutable::now());
    }

    public function today(): CarbonImmutable
    {
        return $this->now()->startOfDay();
    }

    public function fromReal(CarbonInterface $real): CarbonImmutable
    {
        $elapsed = $real->getTimestamp() - $this->realEpoch()->getTimestamp();

        return $this->gameEpoch()->addSeconds($elapsed * $this->scale());
    }

    public function toReal(CarbonInterface $game): CarbonImmutable
    {
        $elapsed = $game->getTimestamp() - $this->gameEpoch()->getTimestamp();

        return $this->realEpoch()->addSeconds((int) ceil($elapsed / $this->scale()));
    }

    public function display(CarbonInterface $real): string
    {
        return $this->fromReal($real)->format(self::DISPLAY_FORMAT);
    }

    /**
     * @return array{scale: int, real_epoch: string, game_epoch: string}
     */
    public function settings(): array
    {
        return [
            'scale' => $this->scale(),
            'real_epoch' => $this->realEpoch()->toIso8601String(),
            'game_epoch' => $this->gameEpoch()->format(self::DISPLAY_FORMAT),
        ];
    }

    private function scale(): int
    {
        return max(1, (int) config('game.clock.scale'));
    }

    private function realEpoch(): CarbonImmutable
    {
        return CarbonImmutable::parse((string) config('game.clock.real_epoch'), 'UTC');
    }

    private function gameEpoch(): CarbonImmutable
    {
        return CarbonImmutable::parse((string) config('game.clock.game_epoch'), 'UTC');
    }
}
