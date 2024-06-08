<?php

namespace App\Services;

use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\ThreedSetting;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminThirdPrizeService
{
    public function GetRecordForOneWeek()
    {

        // Get the match start date and result date from ThreedSetting
        $draw_date = ThreedSetting::where('status', 'open')->first();
        $start_date = $draw_date->match_start_date;
        $end_date = $draw_date->result_date;

        // Retrieve records within the specified date range and include user information
        $records = LotteryThreeDigitPivot::select('lottery_three_digit_pivots.*', 'users.name', 'users.phone', DB::raw('lottery_three_digit_pivots.sub_amount * 10 as prize_amount'))
            ->join('users', 'lottery_three_digit_pivots.user_id', '=', 'users.id')

            ->where('lottery_three_digit_pivots.prize_sent', 3)
            ->whereBetween('lottery_three_digit_pivots.match_start_date', [$start_date, $end_date])
            ->whereBetween('lottery_three_digit_pivots.res_date', [$start_date, $end_date])
            ->get();

        $total_prize_amount = $records->sum('prize_amount');

        // Return the records and total sub_amount
        return [
            'records' => $records,
            'total_prize_amount' => $total_prize_amount,
        ];
    }
}
