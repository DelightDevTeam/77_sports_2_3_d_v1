<?php

namespace App\Http\Controllers\Api\V1\TwoD;

use App\Http\Controllers\Controller;
use App\Services\ApiMorningWinService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MorningWinPrizeController extends Controller
{
    protected $apiMorningWinService;

    public function __construct(ApiMorningWinService $apiMorningWinService)
    {
        $this->apiMorningWinService = $apiMorningWinService;
    }

    /**
     * Get morning prize sent data for the authenticated user.
     */
    public function getMorningPrizeSent(): JsonResponse
    {
        if (! Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $data = $this->apiMorningWinService->MorningPrizeSent();

        return response()->json([
            'status' => 'Request was successful.',
            'message' => null,
            'data' => $data,
        ]);
    }
}
