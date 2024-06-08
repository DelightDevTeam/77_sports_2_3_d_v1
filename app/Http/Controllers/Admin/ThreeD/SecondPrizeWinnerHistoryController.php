<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Services\AdminSecondPrizeService;
use Illuminate\Http\Request;

class SecondPrizeWinnerHistoryController extends Controller
{
    protected $lottoService;

    public function __construct(AdminSecondPrizeService $lottoService)
    {
        $this->lottoService = $lottoService;
    }

    public function SecondWinnerHistories()
    {
        try {
            $data = $this->lottoService->GetRecordForOneWeek();

            return view('admin.three_d.winner.second_prize', [
                'records' => $data['records'],
                'total_prize_amount' => $data['total_prize_amount'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
