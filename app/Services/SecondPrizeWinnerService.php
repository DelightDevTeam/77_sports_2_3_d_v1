<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\ThreeDigit\ThreedSetting;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;

class SecondPrizeWinnerService
{
     public function GetRecordForOneWeek()
    {
        // Get the authenticated user's ID
        $userId = Auth::id();

        // Get the match start date and result date from ThreedSetting
        $draw_date = ThreedSetting::where('status', 'open')->first();
        $start_date = $draw_date->match_start_date;
        $end_date = $draw_date->result_date;

        // Retrieve records within the specified date range and include user information
        $records = LotteryThreeDigitPivot::select('lottery_three_digit_pivots.*', 'users.name', 'users.phone')
            ->join('users', 'lottery_three_digit_pivots.user_id', '=', 'users.id')
            ->where('lottery_three_digit_pivots.user_id', $userId)
            ->where('lottery_three_digit_pivots.prize_sent', 2)
            ->whereBetween('lottery_three_digit_pivots.match_start_date', [$start_date, $end_date])
            ->whereBetween('lottery_three_digit_pivots.res_date', [$start_date, $end_date])
            ->get();

        // Calculate the total sub_amount
        $total_sub_amount = $records->sum('sub_amount');

        // Return the records and total sub_amount
        return [
            'records' => $records,
            'total_sub_amount' => $total_sub_amount,
        ];
    }
}
