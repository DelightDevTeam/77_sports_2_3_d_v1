<?php
namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\Lottery;
use App\Models\Admin\TwoDigit;

class UserHistoryService
{
    public function getUserHistoryForMonth()
    {
        // Set the time zone to Myanmar Time
        $timezone = 'Asia/Yangon';

        // Define start and end of the current month with the desired time zone
        $startOfMonth = Carbon::now($timezone)->startOfMonth();
        $endOfMonth = Carbon::now($timezone)->endOfMonth();

        // Get the authenticated user
        $user = Auth::user();

        // Retrieve the user's data based on the defined time range
        $userHistory = Lottery::where('user_id', $user->id)
                              ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                              ->with(['twoDigits' => function ($query) use ($startOfMonth, $endOfMonth) {
                                  $query->wherePivotBetween('created_at', [$startOfMonth, $endOfMonth]);
                              }])
                              ->get();

        return [
            'status' => 'Request was successful.',
            'message' => null,
            'data' => $userHistory,
        ];
    }
}