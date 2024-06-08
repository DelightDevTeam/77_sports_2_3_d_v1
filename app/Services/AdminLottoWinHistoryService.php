<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;

class AdminLottoWinHistoryService
{
    public function GetRecordForOneWeek()
    {
        // Retrieve records within the specified date range and include user information
        $records = LotteryThreeDigitPivot::select(
                'lottery_three_digit_pivots.*',
                'users.name',
                'users.phone',
                DB::raw('lottery_three_digit_pivots.sub_amount * 700 as prize_amount')
            )
            ->join('users', 'lottery_three_digit_pivots.user_id', '=', 'users.id')
            ->where('lottery_three_digit_pivots.prize_sent', true)
            ->get();

        // Calculate the total prize amount
        $total_prize_amount = $records->sum('prize_amount');

        // Return the records and total prize amount
        return [
            'records' => $records,
            'total_prize_amount' => $total_prize_amount,
        ];
    }
}
