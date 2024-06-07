<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiMorningWinService
{
    /**
     * Retrieve all user data with prize_sent as true, filtered by session and current day.
     *
     * @return array
     */
    public function MorningPrizeSent()
    {
        $today = Carbon::today()->toDateString();
        $userId = Auth::id(); // Get the authenticated user's ID

        try {
            $results = DB::table('lottery_two_digit_pivots')
                ->join('users', 'lottery_two_digit_pivots.user_id', '=', 'users.id')
                ->select(
                    'users.name as user_name',
                    'users.phone as user_phone',
                    'lottery_two_digit_pivots.bet_digit',
                    'lottery_two_digit_pivots.res_date',
                    'lottery_two_digit_pivots.sub_amount',
                    'lottery_two_digit_pivots.session',
                    'lottery_two_digit_pivots.res_time',
                    'lottery_two_digit_pivots.prize_sent'
                )
                ->where('lottery_two_digit_pivots.prize_sent', true)
                ->where('lottery_two_digit_pivots.res_date', $today)
                ->where('lottery_two_digit_pivots.session', 'morning')
                ->where('lottery_two_digit_pivots.user_id', $userId) // Filter by authenticated user
                ->get();

            // Calculate total prize amount

            $totalPrizeAmount = 0;
            //$prizeAmount = 0;
            foreach ($results as $result) {
                $prizeAmount = $result->sub_amount * 85; // Prize multiplier
                $result->win_prize = $prizeAmount;
                $totalPrizeAmount += $prizeAmount;
            }

            return [
                'results' => $results,
                'totalPrizeAmount' => $totalPrizeAmount,
                //'prizeAmount' => $prizeAmount
            ];

        } catch (\Exception $e) {
            Log::error('Error retrieving prize_sent data: '.$e->getMessage());

            return ['results' => collect([]), 'totalPrizeAmount' => 0];
        }
    }
}
