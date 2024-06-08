<?php

namespace App\Services;

use App\Helpers\DrawDateHelper;
use App\Helpers\MatchTimeHelper;
use App\Models\Admin\ThreeDDLimit;
use App\Models\ThreeDigit;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\Lotto;
use App\Models\ThreeDigit\ThreedSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LottoService
{
    public function play($totalAmount, $amounts)
    {
        // Begin Transaction
        DB::beginTransaction();

        try {
            $user = Auth::user();

            if ($user->balance < $totalAmount) {
                // throw new \Exception('Insufficient balance.');
                return 'Insufficient funds.';
            }

            $preOver = [];
            foreach ($amounts as $amount) {
                $preCheck = $this->preProcessAmountCheck($amount);
                if (is_array($preCheck)) {
                    $preOver[] = $preCheck[0];
                }
            }
            if (! empty($preOver)) {
                return $preOver;
            }
            // Create a new lottery entry
            $currentDate = Carbon::now()->format('Y-m-d'); // Format the date and time as needed
            $currentTime = Carbon::now()->format('H:i:s');
            $customString = '77-sport-3d';
            $randomNumber = rand(1, 99999); // Generate a random 4-digit number
            $slipNo = $randomNumber.'-'.$customString.'-'.$currentDate.'-'.$currentTime; // Combine date, string, and random number
            //$lottery = $this->createLottery($totalAmount, $user->id);
            $lottery = Lotto::create([
                'total_amount' => $totalAmount,
                'user_id' => $user->id,
                'slip_no' => $slipNo,
            ]);

            $over = [];
            foreach ($amounts as $amount) {
                $check = $this->processAmount($amount, $lottery->id);
                if (is_array($check)) {
                    $over[] = $check[0];
                }
            }
            if (! empty($over)) {
                return $over;
            }

            $user->decrement('balance', $totalAmount);

            DB::commit();

            // return $lottery;
        } catch (\Exception $e) {
            DB::rollback();

            //throw $e;
            return response()->json(['message' => $e->getMessage()], 401);
            //  return $e->getMessage();
        }
    }

    protected function preProcessAmountCheck($item)
    {
        $num = str_pad($item['num'], 3, '0', STR_PAD_LEFT);
        $sub_amount = $item['amount'];
        $three_digit = ThreeDigit::where('three_digit', $num)->firstOrFail();
        $totalBetAmount = DB::table('lottery_three_digit_copies')->where('three_digit_id', $three_digit->id)->sum('sub_amount');
        $break = ThreeDDLimit::latest()->first()->three_d_limit;

        if ($totalBetAmount + $sub_amount > $break) {
            // throw new \Exception("The bet amount for number $num exceeds the limit.");
            return [$item['num']];
        }
    }

    protected function processAmount($item, $lotteryId)
    {
        $num = str_pad($item['num'], 3, '0', STR_PAD_LEFT);
        $sub_amount = $item['amount'];
        $bet_digit = $item['num'];

        // Find the corresponding three digit record
        $three_digit = ThreeDigit::where('three_digit', $num)->firstOrFail();

        // Calculate the total bet amount for the three_digit
        $totalBetAmount = DB::table('lottery_three_digit_copies')
            ->where('three_digit_id', $three_digit->id)
            ->sum('sub_amount');

        // Check if the limit is exceeded
        $break = ThreeDDLimit::latest()->first()->three_d_limit;
        $draw_date = DrawDateHelper::getResultDate();
        $start_date = $draw_date['match_start_date'];
        $end_date = $draw_date['result_date'];
        $play_date = Carbon::now()->format('Y-m-d');  // Correct date format
        $play_time = Carbon::now()->format('H:i:s');  // Correct time format
        $player_id = Auth::user()->id;
        $results = ThreedSetting::where('status', 'open')
            ->whereBetween('result_date', [$start_date, $end_date])
            ->first();

        if ($results && $results->status == 'closed') {
            return response()->json(['message' => '3D game does not open for this time']);
        }
        $matchTimes = MatchTimeHelper::getCurrentYearAndMatchTimes();

        if (empty($matchTimes['currentMatchTime'])) {
            return response()->json(['message' => 'No current match time available']);
        }

        $currentMatchTime = $matchTimes['currentMatchTime'];
        //Log::info('Running Match Time ID: ' . $currentMatchTime['id'] . ' - Time: ' . $currentMatchTime['match_time']);

        if ($totalBetAmount + $sub_amount <= $break) {

            // Create a pivot record for a valid bet
            $pivot = new LotteryThreeDigitPivot([
                'threed_setting_id' => $results->id,
                'lotto_id' => $lotteryId,
                'three_digit_id' => $three_digit->id,
                'threed_match_time_id' => $currentMatchTime['id'],
                'user_id' => $player_id,
                'bet_digit' => $num,
                'sub_amount' => $sub_amount,
                'prize_sent' => false,
                'match_status' => $results->status,
                'play_date' => $play_date,
                'play_time' => $play_time,
                'res_date' => $results->result_date,
                'res_time' => $results->result_time,
                'match_start_date' => $start_date,
                'running_match' => $currentMatchTime['match_time'],
            ]);
            $pivot->save();
        } else {
            return [$item['num']];
            // throw new \Exception('The bet amount exceeds the limit.');
            // return response()->json(['message'=> 'သတ်မှတ်ထားသော limit ပမာဏထပ်ကျော်လွန်နေပါသည်။'], 401);
        }

        // Perform additional actions if necessary
        // ...
    }
}
