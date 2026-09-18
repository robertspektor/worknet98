<?php

namespace App\Console\Commands;

use App\Metrics\Funnel;
use App\Metrics\FunnelRow;
use App\Metrics\ReturnRate;
use App\Metrics\SessionSummary;
use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('metrics:report {--days=30 : Days of sessions to summarise}')]
#[Description('Print the alpha retention numbers: who comes back, how long a session lasts and where players stop')]
class ReportMetrics extends Command
{
    public function handle(ReturnRate $returns, SessionSummary $sessions, Funnel $funnel): int
    {
        $days = (int) $this->option('days');

        $this->line("DeskLife 98 metrics, real time, {$days} day window");
        $this->newLine();

        $this->components->twoColumnDetail('<options=bold>Players</>', (string) User::query()->count());
        $dayTwo = $returns->dayTwo();
        $this->components->twoColumnDetail('Came back on day two', "{$dayTwo->returned} of {$dayTwo->eligible} ({$dayTwo->percentage()}%)");
        $this->newLine();

        $lengths = $sessions->sinceDays($days);
        $this->components->twoColumnDetail('<options=bold>Sessions</>', (string) $lengths->sessions);
        $this->components->twoColumnDetail('Median length', "{$lengths->medianMinutes} min");
        $this->components->twoColumnDetail('Longest', "{$lengths->longestMinutes} min");
        $this->components->twoColumnDetail('Per player', (string) $lengths->perPlayer());
        $this->newLine();

        $this->line('<options=bold>Funnel</>');
        foreach ($funnel->steps() as $row) {
            $this->components->twoColumnDetail($row->label, $this->reached($row));
        }

        return self::SUCCESS;
    }

    private function reached(FunnelRow $row): string
    {
        return $row->lost > 0 ? "{$row->players}  <fg=red>-{$row->lost}</>" : (string) $row->players;
    }
}
