<?php

namespace App\Http\Controllers\Api\V1\ThreeD;

use App\Http\Controllers\Controller;
use App\Services\ApiLottoOneWeekRecordService;
use Illuminate\Http\Request;

class OneWeekRecordHistoryController extends Controller
{
    protected $lottoService;

    public function __construct(ApiLottoOneWeekRecordService $lottoService)
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
