<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\ThreeD\ThreedSetting;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;

class LottoHistoryRecordService
{
    public function GetRecord()
    {
        $records = LotteryThreeDigitPivot::with('user')->orderBy('id', 'desc')->get();

        // Calculate the total sub_amount
        $total_sub_amount = $records->sum('sub_amount');

        // Return the records and total sub_amount
        return [
            'records' => $records,
            'total_sub_amount' => $total_sub_amount,
        ];
    }
}
