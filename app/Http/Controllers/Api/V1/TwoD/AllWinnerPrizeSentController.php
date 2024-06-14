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

        $winner_lists = $data['results'];
        $lists = [];
        
        foreach($winner_lists as $list) {
            $lists[] = (object)[
                'name' => $list->user_name,
                'res_date' => $list->res_date,
                'prize_amount' => $list->win_prize
            ];
        }
        
        return response()->json([
            'status' => 'Request was successful.',
            'message' => null,
            'data' => $lists,
        ]);
    }
}
