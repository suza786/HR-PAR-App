<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class WeeklyBreakdownService
{
    /**
     * সপ্তাহের ছুটির দিন: শুক্রবার (5) ও শনিবার (6)
     * Carbon: 0=Sunday, 1=Monday, ..., 5=Friday, 6=Saturday
     */
    private array $weekendDays = [5, 6]; // Friday, Saturday

    /**
     * নির্দিষ্ট বছর ও মাসের সাপ্তাহিক তথ্য বের করে
     *
     * @param int $year   বছর (যেমন: 2025)
     * @param int $month  মাস (1-12)
     * @return Collection সপ্তাহের তথ্যের কালেকশন
     */
    public function getWeeklyBreakdown(int $year, int $month): Collection
    {
        // মাসের প্রথম ও শেষ দিন
        $firstDayOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $lastDayOfMonth  = Carbon::create($year, $month, 1)->endOfMonth()->startOfDay();

        // সপ্তাহ শুরু রবিবার (weekStartsAt = Carbon::SUNDAY)
        // প্রথম সপ্তাহের শুরু: মাসের প্রথম দিন থেকে পেছনে গিয়ে রবিবার খোঁজা
        $weekStart = $firstDayOfMonth->copy();
        if ($weekStart->dayOfWeek !== Carbon::SUNDAY) {
            $weekStart->previous(Carbon::SUNDAY);
        }

        $weeks      = collect();
        $weekNumber = 1;

        while ($weekStart->lte($lastDayOfMonth)) {
            // সপ্তাহের শেষ দিন = শনিবার (রবিবার + ৬ দিন)
            $weekEnd = $weekStart->copy()->addDays(6);

            // মাসের বাইরের অংশ বাদ দিয়ে শুধু মাসের মধ্যে থাকা দিনগুলো
            $effectiveStart = $weekStart->lt($firstDayOfMonth)
                ? $firstDayOfMonth->copy()
                : $weekStart->copy();

            $effectiveEnd = $weekEnd->gt($lastDayOfMonth)
                ? $lastDayOfMonth->copy()
                : $weekEnd->copy();

            // মোট দিন ও কর্মদিন গণনা
            [$totalDays, $workingDays] = $this->countDays($effectiveStart, $effectiveEnd);

            $weeks->push([
                'week_number'  => $weekNumber,
                'start_date'   => $effectiveStart->format('Y-m-d'),
                'end_date'     => $effectiveEnd->format('Y-m-d'),
                'start_day'    => $effectiveStart->format('l'),   // e.g. Sunday
                'end_day'      => $effectiveEnd->format('l'),     // e.g. Saturday
                'total_days'   => $totalDays,
                'working_days' => $workingDays,
            ]);

            // পরের রবিবারে যাও
            $weekStart->addDays(7);
            $weekNumber++;
        }

        return $weeks;
    }

    /**
     * দুটি তারিখের মধ্যে মোট দিন ও কর্মদিন গণনা করে
     *
     * নিয়ম:
     *  - শুক্রবার (5) ও শনিবার (6) ছুটির দিন
     *  - যদি সপ্তাহে শুধু শুক্র/শনি থাকে → কর্মদিন = 0
     *
     * @return array [totalDays, workingDays]
     */
    private function countDays(Carbon $start, Carbon $end): array
    {
        $totalDays   = 0;
        $workingDays = 0;

        $current = $start->copy();

        while ($current->lte($end)) {
            $totalDays++;

            if (!in_array($current->dayOfWeek, $this->weekendDays)) {
                $workingDays++;
            }

            $current->addDay();
        }

        return [$totalDays, $workingDays];
    }

    /**
     * সারসংক্ষেপ পরিসংখ্যান সহ ফলাফল প্রদান
     *
     * @param int $year
     * @param int $month
     * @return array
     */
    public function getSummary(int $year, int $month): array
    {
        $weeks = $this->getWeeklyBreakdown($year, $month);

        return [
            'year'              => $year,
            'month'             => $month,
            'month_name'        => Carbon::create($year, $month, 1)->format('F'),
            'total_weeks'       => $weeks->count(),
            'total_days'        => $weeks->sum('total_days'),
            'total_working_days'=> $weeks->sum('working_days'),
            'total_off_days'    => $weeks->sum('total_days') - $weeks->sum('working_days'),
            'weeks'             => $weeks,
        ];
    }
}
