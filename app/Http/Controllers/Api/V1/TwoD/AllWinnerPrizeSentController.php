<?php

namespace App\Http\Controllers\Api\V1\TwoD;

use App\Http\Controllers\Controller;
use App\Services\ApiEveningWinService;
use App\Services\TwodAllWinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AllWinnerPrizeSentController extends Controller
{
    protected $apiAllWinService;

    public function __construct(TwodAllWinService $apiAllWinService)
    {
        $this->apiAllWinService = $apiAllWinService;
    }

    /**
     * Get morning prize sent data for the authenticated user.
     */
    public function getAllWinnerPrizeSent(): JsonResponse
    {

        $data = $this->apiAllWinService->AllWinPrizeSent();

        return response()->json([
            'status' => 'Request was successful.',
            'message' => null,
            'data' => $data,
        ]);
    }
}
