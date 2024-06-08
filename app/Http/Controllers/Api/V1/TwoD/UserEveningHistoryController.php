<?php

namespace App\Http\Controllers\Api\V1\TwoD;

use App\Http\Controllers\Controller;
use App\Services\UserEveningHistoryService;
use Illuminate\Http\Request;

class UserEveningHistoryController extends Controller
{
    protected $eveningHistoryService;

    public function __construct(UserEveningHistoryService $eveningHistoryService)
    {
        $this->eveningHistoryService = $eveningHistoryService;
    }

    /**
     * Fetches the morning history for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $eveningHistory = $this->eveningHistoryService->getEveningHistory();

        if ($eveningHistory['success']) {
            return response()->json($eveningHistory);
        } else {
            return response()->json($eveningHistory, 500);
        }
    }
}
