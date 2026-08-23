<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequested;
use Illuminate\Http\Request;

class LeaveRequestedController extends Controller
{
    public function currentYear()
    {
        return LeaveRequested::whereYear('curr_date', now()->year)->get();
    }

    // public function search(Request $request)
    // {
    //     $request->validate([
    //         'emp_id' => 'required',
    //         'status' => 'required',
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date',
    //     ]);

    //     return LeaveRequested::where('emp_id', $request->emp_id)
    //         ->where('status', $request->status)
    //         ->where('start_date', '>=', $request->start_date)
    //         ->where('end_date', '<=', $request->end_date)
    //         ->get();
    // }

    public function search(Request $request)
    {
        $request->validate([
            'emp_id' => 'required',
        ]);
        return LeaveRequested::where('emp_id', $request->emp_id)->get();
    }

    public function userLeaves(Request $request)
    {
        $request->validate([
            'emp_id' => 'required',
        ]);
        return LeaveRequested::where('emp_id', $request->emp_id)
            ->where('status', 'approved') // Filter by approved status
            ->whereYear('curr_date', now()->year) // Filter by current year only
            ->get();
    }
}