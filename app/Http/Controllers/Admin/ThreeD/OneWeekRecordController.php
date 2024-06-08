<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use App\Models\ThreeDigit\Lotto;
use App\Models\ThreeDigit\ThreedSetting;
use App\Services\LottoOneWeekRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OneWeekRecordController extends Controller
{
    protected $lottoService;

    public function __construct(LottoOneWeekRecordService $lottoService)
    {
        $this->lottoService = $lottoService;
    }

    public function index()
    {
        // Get the match start date and result date from ThreedSetting
        $draw_date = ThreedSetting::where('status', 'open')->first();
        $start_date = $draw_date->match_start_date;
        $end_date = $draw_date->result_date;

        // Retrieve and group records by user_id within the specified date range
        $records = LotteryThreeDigitPivot::with('user')
            ->join('lottos', 'lottery_three_digit_pivots.lotto_id', '=', 'lottos.id')
            ->whereBetween('lottery_three_digit_pivots.match_start_date', [$start_date, $end_date])
            ->whereBetween('lottery_three_digit_pivots.res_date', [$start_date, $end_date])
            ->select('lottery_three_digit_pivots.user_id', 'lottos.slip_no', DB::raw('SUM(lottery_three_digit_pivots.sub_amount) as total_sub_amount'))
            ->groupBy('lottery_three_digit_pivots.user_id', 'lottos.slip_no')
            ->get();
        // Calculate the total amount from the lottos table within the date range
        $total_amount = Lotto::whereBetween('created_at', [$start_date, $end_date])
            ->sum('total_amount');
        //return response()->json($records, $total_amount);

        // You can return the records to your view
        return view('admin.three_d.slip.one_week_slip', compact('records', 'total_amount'));
    }

    public function show($user_id, $slip_no)
    {
        // Retrieve records for a specific user_id and slip_no
        $records = LotteryThreeDigitPivot::with('user')
            ->join('lottos', 'lottery_three_digit_pivots.lotto_id', '=', 'lottos.id')
            ->where('lottery_three_digit_pivots.user_id', $user_id)
            ->where('lottos.slip_no', $slip_no)
            ->select('lottery_three_digit_pivots.*', 'lottos.slip_no')
            ->get();

        // Calculate the total sub_amount for the specific user_id and slip_no
        $total_sub_amount = $records->sum('sub_amount');

        return view('admin.three_d.slip.slip_show', compact('records', 'total_sub_amount', 'slip_no', 'user_id'));
    }

    public function showRecordsForOneWeek()
    {
        $data = $this->lottoService->GetRecordForOneWeek();
        //$total_sub_amount = $this->lottoService->GetRecordForOneWeek();

        return view('admin.three_d.slip.one_week_rec', compact('data'));
    }
}
