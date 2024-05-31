<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\LotteryThreeDigitCopy;
use App\Models\ThreeDigit\Permutation;
use App\Models\ThreeDigit\Prize;
use Illuminate\Http\Request;

class ThreeDResetController extends Controller
{
    public function ThreeDReset()
    {
        LotteryThreeDigitCopy::truncate();
        Permutation::truncate();
        Prize::truncate();
        session()->flash('SuccessRequest', 'Successfully 3D Reset.');

        return redirect()->back()->with('message', 'Data reset successfully!');
    }
}
