<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Services\ThreeDigitDataService;
use Illuminate\Http\Request;

class ThreeDLegarController extends Controller
{
    protected $lotteryService;

    public function __construct(ThreeDigitDataService $lotteryService)
    {
        $this->lotteryService = $lotteryService;
    }

    public function showData()
    {
        //$sessionsData = $this->lotteryService->getThreeDigitsData();
        // In your controller
        $sessionsData = $this->lotteryService->getThreeDigitsData() ?? [];

        // Temporarily add this to check the structure of $sessionsData
        //dd($sessionsData);

        return view('admin.three_d.legar.lejar', [
            'data' => $sessionsData,
        ]);
    }
}
