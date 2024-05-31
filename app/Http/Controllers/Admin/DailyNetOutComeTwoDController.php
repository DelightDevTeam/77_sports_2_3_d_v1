<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Lottery;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyNetOutComeTwoDController extends Controller
{
    protected $twodWiner;

    public function __construct($twodWiner)
    {
        $this->twodWiner = $twodWiner;
    }

    public function calculateNetOutcome()
    {
        $dateToday = Carbon::today();

        // Get Daily Income
        $dailyIncome = Lottery::whereDate('created_at', $dateToday)->sum('total_amount');

        // Get Daily Outcome (Prizes given out)
        $winningEntries = DB::table('lottery_two_digit_copy')
            ->join('lotteries', 'lottery_two_digit_copy.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_copy.two_digit_id', $this->twodWiner->prize_no)
            ->where('lottery_two_digit_copy.prize_sent', 0)
            ->whereDate('lottery_two_digit_copy.created_at', $dateToday)
            ->select('lottery_two_digit_copy.sub_amount')
            ->get();

        $dailyOutcome = 0;
        foreach ($winningEntries as $entry) {
            $dailyOutcome += $entry->sub_amount * 85;  // Prize calculation based on your multiplier
        }

        $netOutcome = $dailyIncome - $dailyOutcome;

        return view('net_outcome', ['netOutcome' => $netOutcome]);
    }
}
