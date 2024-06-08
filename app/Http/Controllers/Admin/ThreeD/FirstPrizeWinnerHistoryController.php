<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Services\AdminFirstPrizeService;
use Illuminate\Http\Request;

class FirstPrizeWinnerHistoryController extends Controller
{
    protected $lottoService;

    public function __construct(AdminFirstPrizeService $lottoService)
    {
        $this->lottoService = $lottoService;
    }

    public function FirstWinnerHistories()
    {
        try {
            $data = $this->lottoService->GetRecordForOneWeek();

            return view('admin.three_d.winner.first_prize', [
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
