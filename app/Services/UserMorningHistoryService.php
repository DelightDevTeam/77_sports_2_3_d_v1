<?php

namespace App\Services;

use App\Models\TwoD\LotteryTwoDigitPivot;
use Illuminate\Support\Facades\Auth;

class UserMorningHistoryService
{
    /**
     * Fetches bet_digit and related data for the evening session for the authenticated user, including the total sub_amount.
     *
     * @return array
     */
    public function getMorningHistory()
    {
        try {
            $today = now()->format('Y-m-d'); // Get today's date
            $session = 'morning'; // Define session
            $userId = Auth::id(); // Get the authenticated user's ID

            $data = LotteryTwoDigitPivot::with(['lottery', 'user'])
                ->where('res_date', $today)
                ->where('session', $session)
                ->where('user_id', $userId)
                ->get()
                ->map(function ($pivot) {
                    return [
                        // 'lottery_id' => $pivot->lottery_id,
                        // 'twod_setting_id' => $pivot->twod_setting_id,
                        // 'two_digit_id' => $pivot->two_digit_id,
                        'bet_digit' => $pivot->bet_digit,
                        'sub_amount' => $pivot->sub_amount,
                        // 'match_status' => $pivot->match_status,
                        'play_date' => $pivot->play_date,
                        'play_time' => $pivot->play_time,
                        // 'res_date' => $pivot->res_date,
                        // 'res_time' => $pivot->res_time,
                        'session' => $pivot->session,
                        // 'admin_log' => $pivot->admin_log,
                        // 'user_log' => $pivot->user_log,
                        // 'user_name' => $pivot->user->name,
                        // 'user_phone' => $pivot->user->phone,
                        'lottery_slip_no' => $pivot->lottery->slip_no,
                        // 'lottery_total_amount' => $pivot->lottery->total_amount,
                        'prize_sent' => $pivot->prize_sent,
                        'win_lose' => $pivot->win_lose,
                    ];
                });

            $totalSubAmount = $data->sum('sub_amount');

            return [
                'success' => true,
                'total_sub_amount' => $totalSubAmount,
                'data' => $data,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while fetching morning history: '.$e->getMessage(),
            ];
        }
    }
}
