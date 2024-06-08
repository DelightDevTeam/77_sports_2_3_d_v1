<?php

namespace App\Http\Controllers\Api\V1\ThreeD;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\FirstPrizeWinnerService;

class FirstPrizeWinnerController extends Controller
{
    protected $lottoService;

    public function __construct(FirstPrizeWinnerService $lottoService)
    {
        $this->lottoService = $lottoService;
    }
    public function showRecordsForOneWeek()
{
    try {
        $data = $this->lottoService->GetRecordForOneWeek();
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
