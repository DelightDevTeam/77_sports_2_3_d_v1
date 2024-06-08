<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\Lotto;
use App\Services\LottoHistoryRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ALlHistoryController extends Controller
{
    protected $lottoService;

    public function __construct(LottoHistoryRecordService $lottoService)
    {
        $this->lottoService = $lottoService;
    }

    public function showRecords()
    {
        $data = $this->lottoService->GetRecord();
        //$total_sub_amount = $this->lottoService->GetRecordForOneWeek();

        return view('admin.three_d.history.all_history', compact('data'));
    }

    public function index()
    {

        if (! auth()->check() || ! auth()->user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Retrieve records with the user's name
        $records = LotteryThreeDigitPivot::with('user')
            ->join('lottos', 'lottery_three_digit_pivots.lotto_id', '=', 'lottos.id')
            ->select('lottery_three_digit_pivots.user_id', 'lottos.slip_no', DB::raw('SUM(lottery_three_digit_pivots.sub_amount) as total_sub_amount'))
            ->groupBy('lottery_three_digit_pivots.user_id', 'lottos.slip_no')
            ->get();

        // Check if records are found
        if ($records->isEmpty()) {
            return response()->json(['error' => 'No records found'], 404);
        }

        // Calculate the total amount from the lottos table
        $total_amount = Lotto::sum('total_amount');

        // Return the view with records and total amount
        return view('admin.three_d.history.index', [
            'records' => $records,
            'total_amount' => $total_amount,
        ]);
    }

    public function show($user_id, $slip_no)
    {
        try {
            // Ensure the user is authenticated and is an admin
            if (! auth()->check() || ! auth()->user()->isAdmin()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Retrieve records for a specific user_id and slip_no with the user's name
            $records = LotteryThreeDigitPivot::with('user')
                ->join('lottos', 'lottery_three_digit_pivots.lotto_id', '=', 'lottos.id')
                ->where('lottery_three_digit_pivots.user_id', $user_id)
                ->where('lottos.slip_no', $slip_no)
                ->select('lottery_three_digit_pivots.*', 'lottos.slip_no', 'users.name as user_name')
                ->join('users', 'lottery_three_digit_pivots.user_id', '=', 'users.id')
                ->get();

            // Check if records are found
            if ($records->isEmpty()) {
                return response()->json(['error' => 'No records found'], 404);
            }

            // Calculate the total sub_amount for the specific user_id and slip_no
            $total_sub_amount = $records->sum('sub_amount');

            // Return the view with records and total sub_amount
            return view('admin.three_d.history.show', [
                'records' => $records,
                'total_sub_amount' => $total_sub_amount,
                'slip_no' => $slip_no,
                'user_id' => $user_id,
            ]);

        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error retrieving records for user_id: '.$user_id.' and slip_no: '.$slip_no.'. Error: '.$e->getMessage());

            // Error message
            return response()->json(['error' => 'Failed to retrieve records'], 500);
        }
    }
}
