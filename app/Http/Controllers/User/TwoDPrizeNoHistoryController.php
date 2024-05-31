<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\TwodWiner;
use Illuminate\Http\Request;

class TwoDPrizeNoHistoryController extends Controller
{
    public function index()
    {
        $morningData = TwodWiner::where('session', 'morning')->orderBy('id', 'desc')->get();
        $eveningData = TwodWiner::where('session', 'evening')->orderBy('id', 'desc')->get();

        return view('frontend.morning_prize_no_history', compact('morningData', 'eveningData'));
    }
}
