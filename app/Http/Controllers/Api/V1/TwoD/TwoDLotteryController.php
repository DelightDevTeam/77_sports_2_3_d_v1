<?php

namespace App\Http\Controllers\Api\V1\TwoD;

use App\Helpers\SessionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TwoDPlayRequest;
use App\Models\Admin\CloseTwoDigit;
use App\Models\Admin\HeadDigit;
use App\Models\Admin\TwoDigit;
use App\Models\Admin\TwoDLimit;
use App\Models\TwoD\LotteryTwoDigitCopy;
use App\Models\TwoD\TwodSetting;
use App\Services\TwoDPlayService;
use App\Traits\HttpResponses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TwoDLotteryController extends Controller
{
    use HttpResponses;

    protected $playService;

    public function get_towdigit()
    {
        $digits = TwoDigit::all();

        $over_all_break = TwoDLimit::latest()->first()->two_d_limit;
        $user = Auth::user();
        $user_break = $user->limit;
        $draw_date = TwodSetting::where('status', 'open')->first();
        //$start_date = $draw_date->match_start_date;
        $win_date = $draw_date->result_date;
        $current_session = SessionHelper::getCurrentSession();

        foreach ($digits as $digit) {
            $totalAmount = LotteryTwoDigitCopy::where('two_digit_id', $digit->id)
                ->where('res_date', $win_date)
                ->where('session', $current_session)
                ->sum('sub_amount');
            $over_all_remaining = $over_all_break - $totalAmount;
            $digit->over_all_remaining = $over_all_remaining;
            $user_remaining = $user_break - $totalAmount;
            $digit->user_remaining = $user_remaining;
        }

        return $this->success([
            'two_digits' => $digits,
            'default_break' => $over_all_break,
            'user_break' => $user_break,
        ]);
    }

    public function store(TwoDPlayRequest $request, TwoDPlayService $playService)
    {
        // Log the incoming request for debugging
        //Log::info('Store method called.');

        // Fetch the current open 2D lottery setting
        $currentDate = TwodSetting::where('status', 'open')->first();
        if ($currentDate) {
            Log::info('Current Date is :'.$currentDate->result_date);
        } else {
            Log::info('No current date found.');
        }

        // Check if the current 2D lottery setting is open
        if (! $currentDate || $currentDate->status === 'close') {
            return response()->json([
                'success' => false,
                'message' => 'This 2D lottery match is closed at this time. Welcome back next time!',
            ], 401);
        }

        // Retrieve the validated data from the request
        $totalAmount = $request->input('totalAmount');
        Log::info('The Total Amount is :'.$totalAmount);

        // Log the amounts array
        $amounts = $request->input('amounts');
        Log::info('The Sub Amounts are :'.json_encode($amounts));

        try {
            // fetch all head digits not allowed
            $closeHeadDigits = HeadDigit::query()->get([
                'digit_one', 'digit_two', 'digit_three',
            ])->flatMap(function ($item) {
                return [$item->digit_one, $item->digit_two, $item->digit_three];
            })->unique()
                ->all();
            Log::info('The HeadDigit have been Closed :'.json_encode($closeHeadDigits));

            foreach ($amounts as $amount) {
                $headDigitOfSelected = substr(sprintf('%02d', $amount['num']), 0, 1); // Ensure
                if (in_array($headDigitOfSelected, $closeHeadDigits)) {
                    return response()->json(['message' => "ထိပ်ဂဏန်း '{$headDigitOfSelected}'  ကိုပိတ်ထားသောကြောင့် ကံစမ်း၍ မရနိုင်ပါ ၊ ကျေးဇူးပြု၍ ဂဏန်းပြန်ရွှေးချယ်ပါ။ "], 401);
                }
            }

            $closedTwoDigits = CloseTwoDigit::query()
                ->pluck('digit')
                ->map(function ($digit) {
                    // Ensure formatting as a two-digit string
                    return sprintf('%02d', $digit);
                })
                ->unique()
                ->filter()
                ->values()
                ->all();

            Log::info('The Digit have been Closed :'.json_encode($closedTwoDigits));

            foreach ($request->input('amounts') as $amount) {
                $twoDigitOfSelected = sprintf('%02d', $amount['num']); // Ensure two-digit format
                if (in_array($twoDigitOfSelected, $closedTwoDigits)) {
                    return response()->json(['message' => "2D -  '{$twoDigitOfSelected}'  ကိုပိတ်ထားသောကြောင့် ကံစမ်း၍ မရနိုင်ပါ ၊ ကျေးဇူးပြု၍ ဂဏန်းပြန်ရွှေးချယ်ပါ။ "], 401);
                }
            }

            $result = $playService->play($totalAmount, $amounts);
            if ($result === 'Insufficient funds.') {
                // Insufficient funds message
                return response()->json(['message' => 'လက်ကျန်ငွေ မလုံလောက်ပါ။'], 401);
            }

            if (is_array($result) && ! empty($result)) {
                $digitStrings = collect($result)->implode(', '); // Over-limit digits
                $message = "သင့်ရွှေးချယ်ထားသော {$digitStrings} ဂဏန်းမှာ သတ်မှတ် အမောင့်ထက်ကျော်လွန်ပါသောကြောင့် ကံစမ်း၍မရနိုင်ပါ။";

                return response()->json(['message' => $message], 401);
            }

            // If $result is neither "Insufficient funds." nor an array, assuming success.
            return $this->success($result);
        } catch (\Exception $e) {
            // In case of an exception, return an error response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        }

    }
}
