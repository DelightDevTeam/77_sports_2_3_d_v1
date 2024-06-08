<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Services\AdminLottoWinHistoryService;
use Illuminate\Http\Request;

class AllWinnerHistoryController extends Controller
{
    protected $lottoService;

    public function __construct(AdminLottoWinHistoryService $lottoService)
    {
        $this->lottoService = $lottoService;
    }

    public function AllWinnerHistories()
    {
        try {
            $data = $this->lottoService->GetRecordForOneWeek();

            return view('admin.three_d.winner.all_win_history', [
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
