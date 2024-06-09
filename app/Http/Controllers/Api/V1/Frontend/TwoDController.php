<?php

namespace App\Http\Controllers\Api\V1\Frontend;

use App\Helpers\SessionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TwoDPlayRequest;
use App\Models\Admin\CloseTwoDigit;
use App\Models\Admin\Currency;
use App\Models\Admin\HeadDigit;
use App\Models\Admin\LotteryMatch;
use App\Models\Admin\TwoDigit;
use App\Models\Admin\TwoDLimit;
use App\Models\Lottery;
use App\Models\LotteryTwoDigitPivot;
use App\Models\Two\LotteryTwoDigitOverLimit;
use App\Models\TwoD\LotteryTwoDigitCopy;
use App\Models\TwoD\TwodSetting;
use App\Models\User;
use App\Services\LotteryService;
use App\Services\TwoDLotteryService;
use App\Services\TwoDService;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class TwoDController extends Controller
{
    use HttpResponses;

    protected $lotteryService;

    public function __construct(TwoDLotteryService $lotteryService)
    {
        $this->middleware('auth'); // Ensure user is authenticated
        $this->lotteryService = $lotteryService;
    }

    public function index()
    {
        // $digits = TwoDigit::all();
        // $break = TwoDLimit::latest()->first()->two_d_limit;
        // foreach($digits as $digit)
        // {
        //     $totalAmount = LotteryTwoDigitCopy::where('two_digit_id', $digit->id)->sum('sub_amount');
        //     $remaining = $break-$totalAmount;
        //     $digit->remaining = $remaining;
        // }
        // $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first(['id', 'match_name', 'is_active']);
        // return $this->success([
        //     'break' => $break,
        //     'two_digits' => $digits,
        //     'lottery_matches' => $lottery_matches
        // ]);
        $digits = TwoDigit::all();

        $over_all_break = TwoDLimit::latest()->first()->two_d_limit;
        $user = Auth::user();
        $user_break = $user->limit;
        $draw_date = TwodSetting::where('status', 'open')->first();
        if(!$draw_date){
            return $this->error("error", '2D Session များ ပိတ်ထားပါသည်။', 401);
        }
        //$start_date = $draw_date->match_start_date;
        $win_date = $draw_date->result_date;
        $current_session = SessionHelper::getCurrentSession();

        foreach ($digits as $digit) {
            $totalAmount = LotteryTwoDigitCopy::where('two_digit_id', $digit->id)
                ->where('res_date', $win_date)
                ->where('session', $current_session)
                ->sum('sub_amount');
            $remaining = $over_all_break - $totalAmount;
            $digit->remaining = $remaining;
            $user_remaining = $user_break - $totalAmount;
            $digit->user_remaining = $user_remaining;
        }
        $lottery_matches = LotteryMatch::where('id', 1)->whereNotNull('is_active')->first(['id', 'match_name', 'is_active']);

        return $this->success([
            'two_digits' => $digits,
            'break' => $over_all_break,
            'user_break' => $user_break,
            'lottery_matches' => $lottery_matches,
        ]);

    }

    public function get_towdigit()
    {
        // Fetch all TwoDigit records
        $digits = TwoDigit::all();

        // Retrieve the overall limit and user-specific limit
        $over_all_break = TwoDLimit::latest()->first()->two_d_limit;
        $user = Auth::user();
        $user_break = $user->limit;

        // Get the draw date and current session
        $draw_date = TwodSetting::where('status', 'open')->first();
        if(!$draw_date){
            return $this->error("error", '2D Session များ ပိတ်ထားပါသည်။', 401);
        }
        $win_date = $draw_date->result_date;
        $current_session = SessionHelper::getCurrentSession();

        // Process each digit
        $two_digits = [];
        foreach ($digits as $digit) {
            $totalAmount = LotteryTwoDigitCopy::where('two_digit_id', $digit->id)
                ->where('res_date', $win_date)
                ->where('session', $current_session)
                ->sum('sub_amount');

            $over_all_remaining = $over_all_break - $totalAmount;
            $user_remaining = $user_break - $totalAmount;

            $two_digits[] = [
                'id' => $digit->id,
                'two_digit' => $digit->two_digit,
                'created_at' => $digit->created_at,
                'updated_at' => $digit->updated_at,
                'remaining' => $over_all_remaining,
            ];
        }

        // Construct the response array
        $response = [
            'status' => 'Request was successful.',
            'message' => null,
            'data' => [
                'break' => $over_all_break,
                'two_digits' => $two_digits,
                'lottery_matches' => [
                    'id' => 1,
                    'match_name' => '2D',
                    'is_active' => '1',
                ],
            ],
        ];

        // Return the formatted response
        return response()->json($response);
    }

    public function play(TwoDPlayRequest $request, TwoDService $twoDService): JsonResponse
    {
        //Log::info($request->all());

        // Retrieve the validated data from the request
        $totalAmount = $request->input('totalAmount');
        $amounts = $request->input('amounts');

        try {
            // Fetch all head digits not allowed
            $closedHeadDigits = HeadDigit::query()
                ->get(['digit_one', 'digit_two', 'digit_three'])
                ->flatMap(function ($item) {
                    return [$item->digit_one, $item->digit_two, $item->digit_three];
                })
                ->unique()
                // ->filter()
                // ->values()
                ->all();

            // return response()->json($closedHeadDigits);
            foreach ($amounts as $amount) {
                $headDigitOfSelected = substr(sprintf('%02d', $amount['num']), 0, 1); // Ensure two-digit format and extract the head digit
                // if($headDigitOfSelected < 10 && in_array("0", $closedHeadDigits)){
                //     return response()->json(['message' => "ထိပ်ဂဏန်း '0'  ကိုပိတ်ထားသောကြောင့် ကံစမ်း၍ မရနိုင်ပါ ၊ ကျေးဇူးပြု၍ ဂဏန်းပြန်ရွှေးချယ်ပါ။ "], 401);
                // }
                if (in_array($headDigitOfSelected, $closedHeadDigits)) {
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

            foreach ($request->input('amounts') as $amount) {
                $twoDigitOfSelected = sprintf('%02d', $amount['num']); // Ensure two-digit format
                if (in_array($twoDigitOfSelected, $closedTwoDigits)) {
                    return response()->json(['message' => "2D -  '{$twoDigitOfSelected}'  ကိုပိတ်ထားသောကြောင့် ကံစမ်း၍ မရနိုင်ပါ ၊ ကျေးဇူးပြု၍ ဂဏန်းပြန်ရွှေးချယ်ပါ။ "], 401);
                }
            }

            // Pass the validated data to the TwoDService
            $result = $twoDService->play($totalAmount, $amounts);

            if ($result === 'Insufficient funds.') {
                // Insufficient funds message
                return response()->json(['message' => 'လက်ကျန်ငွေ မလုံလောက်ပါ။'], 401);
            } elseif (is_array($result)) {
                // Process exceeding limit message
                $digitStrings = collect($result)->map(function ($r) {
                    return TwoDigit::find($r)->two_digit + 1 ?? 'Unknown';
                })->implode(',');

                $message = "{$digitStrings} ဂဏန်းမှာ သတ်မှတ် Limit ထက်ကျော်လွန်နေပါသည်။";

                return response()->json(['message' => $message], 401);
            }

            // If $result is neither "Insufficient funds." nor an array, assuming success.
            return $this->success($result);

        } catch (\Exception $e) {
            // In case of an exception, return an error response
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        }
    }

    // protected function success($data)
    // {
    //     // Assuming you have a success method to format your successful response
    //     return response()->json(['data' => $data], 200);
    // }

    public function playHistory(): JsonResponse
    {
        $userId = auth()->id();

        $history12pm = $this->lotteryService->getUserTwoDigits($userId, 'morning');
        $history4pm = $this->lotteryService->getUserTwoDigits($userId, 'evening');

        return response()->json([
            'history12pm' => $history12pm,
            'history4pm' => $history4pm,
        ]);
    }

    // for admin
    public function playHistoryForAdmin(): JsonResponse
    {
        // Example: Fetching history for all users
        $history12pm = $this->lotteryService->getAllUsersTwoDigits('morning');
        $history4pm = $this->lotteryService->getAllUsersTwoDigits('evening');

        return response()->json([
            'history12pm' => $history12pm,
            'history4pm' => $history4pm,
        ]);
    }

    // public function play(Request $request)
    // {
    //     // Log the entire request
    //     Log::info($request->all());
    //     $break = TwoDLimit::latest()->first()->two_d_limit;
    //     // Convert JSON request to an array
    //     $data = $request->json()->all();

    //     // Validate the incoming data
    //     $validator = Validator::make($data, [
    //        // 'currency' => 'required|string',
    //         'totalAmount' => 'required|numeric|min:1',
    //         'amounts' => 'required|array',
    //         'amounts.*.num' => 'required|integer',
    //         'amounts.*.amount' => 'required|integer|min:1',
    //         // 'amounts.*.amount' => 'required|integer|min:1|max:'.$break,
    //     ]);

    //     // Check for validation errors
    //     if ($validator->fails()) {
    //         return response()->json(['message' => $validator->errors()], 401);
    //     }

    //     // Extract validated data
    //     $validatedData = $validator->validated();
    //     // $subAmount = array_sum(array_column($request->amounts, 'amount'));

    //     // if ($subAmount > $break) {
    //     //     return response()->json(['message' => 'Limit ပမာဏထက်ကျော်ထိုးလို့ မရပါ။'], 401);
    //     // }

    //     // Determine the current session based on time
    //     $currentSession = date('H') < 12 ? 'morning' : 'evening';

    //     // Start database transaction
    //     DB::beginTransaction();

    //     try {
    //         $totalAmount = $request->totalAmount;

    //         $user = Auth::user();
    //         $user->balance -= $totalAmount;

    //         // Check if the user has sufficient balance
    //         if ($user->balance < 0) {
    //             return response()->json(['message' => 'လက်ကျန်ငွေ မလုံလောက်ပါ။'], 401);
    //         }
    //         /** @var \App\Models\User $user */
    //         $user->save();

    //         // Create a new lottery entry
    //         $lottery = Lottery::create([
    //             'pay_amount' => $totalAmount,
    //             'total_amount' => $totalAmount,
    //             'user_id' => $user->id, // Use authenticated user's ID
    //             'session' => $currentSession
    //         ]);

    //         $overLimitAmounts = [];
    //         // Iterate through each bet and process it
    //         foreach ($validatedData['amounts'] as $bet) {
    //             $two_digit_id = $bet['num'] === 0 ? 100 : $bet['num']; // Assuming '00' corresponds to 100
    //             $break = TwoDLimit::latest()->first()->two_d_limit;
    //             $totalBetAmountForTwoDigit = DB::table('lottery_two_digit_copy')
    //             ->where('two_digit_id', $two_digit_id)
    //             ->sum('sub_amount');
    //             $sub_amount = $bet['amount'];
    //             $check_limit = $totalBetAmountForTwoDigit + $sub_amount;

    //             if($totalBetAmountForTwoDigit + $sub_amount <= $break){
    //                 $pivot = new LotteryTwoDigitPivot([
    //                     'lottery_id' => $lottery->id,
    //                     'two_digit_id' => $two_digit_id,
    //                     'sub_amount' => $sub_amount,
    //                     'prize_sent' => false
    //                     //'currency' => 'mmk'
    //                 ]);
    //                 $pivot->save();
    //             }else{
    //                 $withinLimit = $break - $totalBetAmountForTwoDigit;
    //                 $overLimit = $sub_amount - $withinLimit;

    //                 if ($withinLimit > 0) {
    //                     $pivotWithin = new LotteryTwoDigitPivot([
    //                         'lottery_id' => $lottery->id,
    //                         'two_digit_id' => $two_digit_id,
    //                         'sub_amount' => $withinLimit,
    //                         'prize_sent' => false
    //                        // 'currency' => 'mmk'
    //                     ]);
    //                     $pivotWithin->save();
    //                       $overLimitAmounts[] = [
    //                         'num' => $bet['num'],
    //                         'amount' => $overLimit,
    //                     ];
    //                 }

    //                 // if ($overLimit > 0) {
    //                 //     $pivotOver = new LotteryTwoDigitOverLimit([
    //                 //         'lottery_id' => $lottery->id,
    //                 //         'two_digit_id' => $two_digit_id,
    //                 //         'sub_amount' => $overLimit,
    //                 //         'prize_sent' => false,
    //                 //         'currency' => 'mmk'

    //                 //     ]);
    //                 //     $pivotOver->save();
    //                 // }
    //             }

    //         // if($check_limit > $break){
    //         //     return response()->json([
    //         //         'message' => 'သတ်မှတ်ထားသော ထိုးငွေပမာဏ ထက်ကျော်လွန်နေပါသည်။'
    //         //     ], 401);
    //         // }else{
    //         //      $pivot = new LotteryTwoDigitPivot([
    //         //             'lottery_id' => $lottery->id,
    //         //             'two_digit_id' => $two_digit_id,
    //         //             'sub_amount' => $sub_amount,
    //         //             'prize_sent' => false,
    //         //             'currency' => 'mmk'
    //         //         ]);
    //         //         $pivot->save();
    //         // }

    //             // if ($totalBetAmountForTwoDigit + $sub_amount <= $break) {
    //             //     $pivot = new LotteryTwoDigitPivot([
    //             //         'lottery_id' => $lottery->id,
    //             //         'two_digit_id' => $two_digit_id,
    //             //         'sub_amount' => $sub_amount,
    //             //         'prize_sent' => false,
    //             //         'currency' => 'mmk'
    //             //     ]);
    //             //     $pivot->save();
    //             // } else {
    //             //     $withinLimit = $break - $totalBetAmountForTwoDigit;
    //             //     $overLimit = $sub_amount - $withinLimit;

    //             //     if ($withinLimit > 0) {
    //             //         $pivotWithin = new LotteryTwoDigitPivot([
    //             //             'lottery_id' => $lottery->id,
    //             //             'two_digit_id' => $two_digit_id,
    //             //             'sub_amount' => $withinLimit,
    //             //             'prize_sent' => false,
    //             //             'currency' => 'mmk'
    //             //         ]);
    //             //         $pivotWithin->save();
    //             //     }

    //             //     if ($overLimit > 0) {
    //             //         $pivotOver = new LotteryTwoDigitOverLimit([
    //             //             'lottery_id' => $lottery->id,
    //             //             'two_digit_id' => $two_digit_id,
    //             //             'sub_amount' => $overLimit,
    //             //             'prize_sent' => false,
    //             //             'currency' => 'mmk'

    //             //         ]);
    //             //         $pivotOver->save();
    //             //     }
    //             // }
    //         }

    //         if(!empty($overLimitAmounts)){
    //                 return response()->json([
    //                     'overLimitAmounts' => $overLimitAmounts,
    //                     'message' => 'သတ်မှတ်ထားသော ထိုးငွေပမာဏ ထက်ကျော်လွန်နေပါသည်။'
    //                 ], 401);
    //             }

    //         // Commit the transaction
    //         DB::commit();

    //         // Return a success response
    //         return $this->success([
    //             'message' => 'Bet placed successfully',
    //         ]);
    //         // return response()->json(['message' => 'Bet placed successfully'], 200);
    //     } catch (\Exception $e) {
    //         // Roll back the transaction in case of error
    //         DB::rollback();
    //         Log::error('Error in play method: ' . $e->getMessage());

    //         // Return an error response
    //         return response()->json(['message' => $e->getMessage()], 401);
    //     }
    // }

    // public function playHistory()
    // {
    //     $userId = auth()->id();
    //     //$history9am = User::getUserEarlyMorningTwoDigits($userId);
    //     $history12pm = User::getUserMorningTwoDigits($userId);
    //     //$history2pm = User::getUserEarlyEveningTwoDigits($userId);
    //     $history4pm = User::getUserEveningTwoDigits($userId);

    //     return $this->success([
    //         //'history9am' => $history9am,
    //         'history12pm' => $history12pm,
    //         //'history2pm' => $history2pm,
    //         'history4pm' => $history4pm,
    //     ]);
    // }
    public function TwoDigitOnceMonthHistory()
    {
        $userId = auth()->id(); // Get logged in user's ID
        $twod_once_month_history = User::getUserOneMonthTwoDigits($userId);

        return $this->success([
            $twod_once_month_history,
        ]);
    }
}
