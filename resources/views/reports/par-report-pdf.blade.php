<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PAR Report</title>
    <style>
        @page { margin: 50px 22px; }
        body { color: #111827; font-family: DejaVu Sans, sans-serif; font-size: 10px; margin: 0; }
        h1 { font-size: 15px; margin: 10px 0 8px; text-align: center; }
        table { margin: 10px 0; border-collapse: collapse; width: 100%; }
        td, th { border: 1px solid #6b7280; padding: 4px; }
        th { background: #e5e7eb; font-weight: bold; }
        hr { margin: 8px 0; border: 0; border-top: 1px solid #6b7280; }
        .meta { margin-bottom: 7px; }
        .meta td { border: 0; padding: 2px 4px; width: 25%; }
        .label { font-weight: bold; }
        .center { text-align: center; }
        .right { text-align: right; }
        .activity { width: 19%; }
        .project { width: 8%; }
        .week-date { font-size: 6.5px; }
        .leave-row td { background: #fffbeb; }
        .total-row td { background: #e5e7eb; font-weight: bold; }
        .details { margin-top: 8px; }
        .details > tbody > tr > td { border: 0; padding: 0 5px 0 0; vertical-align: top; width: 50%; }
        .details > tbody > tr > td:last-child { padding: 0 0 0 5px; }
        .details h2 { font-size: 9px; margin: 0 0 4px; }
        .details table td, .details table th { padding: 3px; }
        .footer { margin-top: 8px; }
        .footer td { border: 0; padding: 2px 0; }
        .note { color: #4b5563; font-size: 7px; margin-top: 6px; }
    </style>
</head>
<body>
    <h1>Personnel Activity Report (PAR)</h1>
    <hr>
    <table class="meta mt-5">
        <tr>
            <td><span class="label">Employee Name:</span> {{ $report['employee']['name'] }}</td>
            <td><span class="label">Job Title:</span> {{ $report['employee']['position'] }}</td>
            <td><span class="label">Location:</span> {{ $report['employee']['location'] }}</td>
        </tr>
        <tr>
            <td><span class="label">Employee ID:</span> {{ $report['employee']['emp_id'] }}</td>
            <td><span class="label">Supervisor:</span> {{ $report['employee']['supervisor'] }}</td>
            <td><span class="label">Month & Year:</span> {{ $report['month'] }}, {{ $report['year'] }}</td>
        </tr>
    </table>

    <hr>

    <table class="report">
        <thead>
            <tr>
                <th rowspan="2" class="activity">Activity Name</th>
                <th rowspan="2" class="project">Project</th>
                @foreach ($report['weeks'] as $week)
                    <th class="center week-date">{{ $week['start'] }} - {{ $week['end'] }}</th>
                @endforeach
                <th rowspan="2" class="center">Total Hours</th>
                <th rowspan="2" class="center">Percentage Distribution</th>
            </tr>
            <tr>
                @foreach ($report['weeks'] as $week)
                    <th class="center">{{ $week['label'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($report['rows'] as $row)
                <tr>
                    <td>{{ $row['activity'] }}</td>
                    <td>{{ $row['project'] }}</td>
                    @foreach ($report['weeks'] as $week)
                        <td class="center">{{ number_format($row['hours'][$week['number']] ?? 0, 2) }}</td>
                    @endforeach
                    <td class="center">{{ number_format($row['total_hours'], 2) }}</td>
                    <td class="center">{{ number_format($row['percentage'], 2) }}%</td>
                </tr>
            @endforeach
            <tr class="leave-row">
                <td colspan="2">Office Holidays / Leave Hours</td>
                @foreach ($report['weeks'] as $week)
                    <td class="center">{{ number_format($report['weekly_leave_hours'][$week['number']] ?? 0, 2) }}</td>
                @endforeach
                <td class="center">{{ number_format($report['total_leave_hours'], 2) }}</td>
                <td></td>
            </tr>
            <tr class="total-row">
                <td colspan="2">Total Number of Work Hours</td>
                @foreach ($report['weeks'] as $week)
                    <td class="center">{{ number_format($report['weekly_totals'][$week['number']] ?? 0, 2) }}</td>
                @endforeach
                <td class="center">{{ number_format($report['total_allocated_hours'], 2) }}</td>
                <td class="center">{{ $report['total_allocated_hours'] > 0 ? '100.00%' : '0.00%' }}</td>
            </tr>
        </tbody>
    </table>

    <table class="details">
        <tr>
            <td>
                <h2>Office Holidays (if any):</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Holiday Name</th>
                            <th> Date </th>
                        </tr>
                    </thead>
                    @forelse ($report['holidays'] as $holiday)
                        <tr>
                            <td>{{ $holiday['name'] }}</td>
                            <td class="right">{{ $holiday['date'] }}</td>
                        </tr>
                    @empty
                        <tr><td>No office holidays recorded.</td></tr>
                    @endforelse
                </table>
            </td>
            <td>
                <h2>Leave Information</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Leave Type</th>
                            <th>Days</th>
                        </tr>
                    </thead>
                    <tbody align="center">
                        @forelse ($report['leaves'] as $leave)
                            <tr>
                                <td>{{ $leave['start'] }}</td>
                                <td>{{ $leave['end'] }}</td>
                                <td>{{ $leave['type'] }}</td>
                                <td class="right">{{ number_format($leave['days'], 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="center">No approved leave recorded.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    <hr>
    <table class="footer mt-5">
        <tr>
            <td><span class="label">PAR Status:</span> {{ $report['status'] }}</td>
            <td class="right"><span class="label">Supervisor:</span> {{ $report['employee']['supervisor'] }}</td>
        </tr>
    </table>
    <p class="note">Note: This is an online system generated copy. No signature is needed.</p>
</body>
</html>
