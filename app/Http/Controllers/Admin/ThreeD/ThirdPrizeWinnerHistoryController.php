<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Services\AdminThirdPrizeService;
use Illuminate\Http\Request;

class ThirdPrizeWinnerHistoryController extends Controller
{
    protected $lottoService;

    public function __construct(AdminThirdPrizeService $lottoService)
    {
        $this->lottoService = $lottoService;
    }

    public function ThirdWinnerHistories()
    {
        try {
            $data = $this->lottoService->GetRecordForOneWeek();

            return view('admin.three_d.winner.third_prize', [
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
