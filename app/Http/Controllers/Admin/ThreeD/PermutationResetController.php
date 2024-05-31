<?php

namespace App\Http\Controllers\Admin\ThreeD;

use App\Http\Controllers\Controller;
use App\Models\ThreeDigit\Permutation;
use Illuminate\Http\Request;

class PermutationResetController extends Controller
{
    public function PermutationReset()
    {
        Permutation::truncate();
        session()->flash('SuccessRequest', 'Successfully 3D Permutation Reset.');

        return redirect()->back()->with('message', 'Data reset successfully!');
    }
}
