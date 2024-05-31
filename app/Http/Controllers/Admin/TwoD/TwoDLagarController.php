<?php

namespace App\Http\Controllers\Admin\TwoD;

use App\Http\Controllers\Controller;
use App\Services\TwoDLagarService;
use Illuminate\Http\Request;

class TwoDLagarController extends Controller
{
    protected $lotteryService;

    public function __construct(TwoDLagarService $lotteryService)
    {
        $this->lotteryService = $lotteryService;
    }

    public function showData()
    {
        $sessionsData = $this->lotteryService->getGroupedDataBySession();

        return view('admin.two_d.lajar.morning_lajar', ['sessionsData' => $sessionsData]);
    }

    public function showDataEvening()
    {
        $sessionsData = $this->lotteryService->getGroupedDataBySession();

        return view('admin.two_d.lajar.evening_lajar', ['sessionsData' => $sessionsData]);
    }

    // public function showDataBySession($session)
    // {
    //     $bets = $this->lotteryService->getAllSessionsData($session);

    //     // Return the data to your view or in another format as needed
    //     return view('admin.two_d.lajar.morning_lajar', ['bets' => $bets]);
    // }
}
