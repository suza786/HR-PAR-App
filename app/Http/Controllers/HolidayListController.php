<?php

namespace App\Http\Controllers;

use App\Models\HolidayList;
use Illuminate\Http\Request;

class HolidayListController extends Controller
{
    // public function index(Request $request)
    // {
    //     return HolidayList::where('leave_year', $request->integer('year', date('Y')))->get();
    // }

    public function index(Request $request)
    {
        return HolidayList::where('leave_year', $request->integer('year', date('Y')))->get();
    }
}
