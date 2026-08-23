<?php

namespace App\Console\Commands;

use App\Services\WeeklyBreakdownService;
use Illuminate\Console\Command;

class WeeklyBreakdownCommand extends Command
{
    protected $signature   = 'weeks:breakdown {year} {month}';
    protected $description = 'Displays weekly and working day breakdown for the specified month';

    public function handle(WeeklyBreakdownService $service): void
    {
        $year  = (int) $this->argument('year');
        $month = (int) $this->argument('month');

        if ($month < 1 || $month > 12) {
            $this->error('Month must be between 1 and 12.');
            return;
        }

        $summary = $service->getSummary($year, $month);

        $this->info("\n📅  {$summary['month_name']} {$year} — Weekly Breakdown\n");

        $rows = $summary['weeks']->map(fn($w) => [
            $w['week_number'],
            $w['start_date'] . ' (' . substr($w['start_day'], 0, 3) . ')',
            $w['end_date']   . ' (' . substr($w['end_day'],   0, 3) . ')',
            $w['total_days'],
            $w['working_days'] === 0
                ? '<fg=red>' . $w['working_days'] . '</>'
                : '<fg=green>' . $w['working_days'] . '</>',
        ])->toArray();

        $this->table(
            ['Week', 'Start', 'End', 'Total Days', 'Working Days'],
            $rows
        );

        $this->line('');
        $this->line("  Total Weeks      : {$summary['total_weeks']}");
        $this->line("  Total Days       : {$summary['total_days']}");
        $this->line("  Total Working Days: <fg=green>{$summary['total_working_days']}</>");
        $this->line("  Total Off Days    : <fg=yellow>{$summary['total_off_days']}</>\n");
    }
}
