<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Admin\LotteryMatch;
use App\Models\Admin\TwodWiner;
use App\Models\Lottery;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TwodPlayIndexController extends Controller
{
    public function index()
    {
        return view('frontend.two_d.twod');
    }

    public function TwoDigitOnceMonthHistory()
    {
        $userId = auth()->id(); // Get logged in user's ID
        $displayJackpotDigit = User::getUserOneMonthTwoDigits($userId);

        return view('two_d.onec_month_two_d_history', [
            'displayThreeDigits' => $displayJackpotDigit,
        ]);
    }
}
