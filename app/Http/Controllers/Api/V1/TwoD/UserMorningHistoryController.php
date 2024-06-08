<?php

namespace App\Http\Controllers\Api\V1\TwoD;

use App\Http\Controllers\Controller;
use App\Services\UserMorningHistoryService;
use Illuminate\Http\Request;

class UserMorningHistoryController extends Controller
{
    protected $morningHistoryService;

    public function __construct(UserMorningHistoryService $morningHistoryService)
    {
        $this->morningHistoryService = $morningHistoryService;
    }

    /**
     * Fetches the morning history for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $morningHistory = $this->morningHistoryService->getMorningHistory();

        if ($morningHistory['success']) {
            return response()->json($morningHistory);
        } else {
            return response()->json($morningHistory, 500);
        }
    }
}
