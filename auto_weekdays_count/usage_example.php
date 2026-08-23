<?php

// ============================================================
// ব্যবহারের উদাহরণ — Controller বা যেকোনো জায়গায়
// ============================================================

// 1. Service সরাসরি inject করে ব্যবহার
use App\Services\WeeklyBreakdownService;

class ReportController extends Controller
{
    public function __construct(
        private WeeklyBreakdownService $weeklyService
    ) {}

    public function weeklyReport(int $year, int $month)
    {
        $summary = $this->weeklyService->getSummary($year, $month);

        return response()->json($summary);
    }
}

// ============================================================
// 2. সরাসরি ব্যবহার (যেকোনো জায়গায়)
// ============================================================

$service = new WeeklyBreakdownService();

// শুধু সপ্তাহের তালিকা
$weeks = $service->getWeeklyBreakdown(2025, 4);

foreach ($weeks as $week) {
    echo "সপ্তাহ {$week['week_number']}: ";
    echo "{$week['start_date']} → {$week['end_date']} | ";
    echo "মোট: {$week['total_days']} দিন | ";
    echo "কর্মদিন: {$week['working_days']}\n";
}

// সারসংক্ষেপ সহ
$summary = $service->getSummary(2025, 4);

echo "মোট সপ্তাহ: {$summary['total_weeks']}\n";
echo "মোট কর্মদিন: {$summary['total_working_days']}\n";

// ============================================================
// 3. Artisan Command থেকে ব্যবহার
// ============================================================
// php artisan weeks:breakdown 2025 4

// ============================================================
// 4. Console Kernel-এ Command রেজিস্ট্রেশন
// ============================================================
// app/Console/Kernel.php → $commands array-এ যোগ করুন:
//
// protected $commands = [
//     \App\Console\Commands\WeeklyBreakdownCommand::class,
// ];

// ============================================================
// নমুনা আউটপুট (এপ্রিল ২০২৫)
// ============================================================
//
// সপ্তাহ ১: 2025-04-01 (Tue) → 2025-04-05 (Sat) | মোট: 5 | কর্মদিন: 3
// সপ্তাহ ২: 2025-04-06 (Sun) → 2025-04-12 (Sat) | মোট: 7 | কর্মদিন: 5
// সপ্তাহ ৩: 2025-04-13 (Sun) → 2025-04-19 (Sat) | মোট: 7 | কর্মদিন: 5
// সপ্তাহ ৪: 2025-04-20 (Sun) → 2025-04-26 (Sat) | মোট: 7 | কর্মদিন: 5
// সপ্তাহ ৫: 2025-04-27 (Sun) → 2025-04-30 (Wed) | মোট: 4 | কর্মদিন: 4
