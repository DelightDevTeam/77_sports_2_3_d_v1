<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\ThreeDigit\ThreedMatchTime;

class MatchTimeHelper
{
    public static function getCurrentYearAndMatchTimes()
    {
        $currentYear = Carbon::now()->year;
        $currentDate = Carbon::now()->toDateString();
        $currentMonth = Carbon::now()->month;
        $currentDay = Carbon::now()->day;

        // Get all match times for the current year
        $yearMatchTimes = ThreedMatchTime::whereYear('result_date', $currentYear)->get();

        // Get the match times for the current month sorted by date
        $currentMonthMatchTimes = ThreedMatchTime::whereYear('result_date', $currentYear)
            ->whereMonth('result_date', $currentMonth)
            ->orderBy('result_date', 'asc')
            ->get();

        // Determine the current match time based on the current date
        $currentMatchTime = null;
        if ($currentDay <= 1) {
            $currentMatchTime = $currentMonthMatchTimes->first();
        } elseif ($currentDay > 1 && $currentDay <= 16) {
            $currentMatchTime = $currentMonthMatchTimes->skip(1)->first();
        } else {
            $nextMonthMatchTimes = ThreedMatchTime::whereYear('result_date', $currentYear)
                ->whereMonth('result_date', $currentMonth + 1)
                ->orderBy('result_date', 'asc')
                ->get();

            $currentMatchTime = $nextMonthMatchTimes->first();
        }

        return [
            'currentYear' => $currentYear,
            'yearMatchTimes' => $yearMatchTimes,
            'currentMatchTime' => $currentMatchTime ? [
                'id' => $currentMatchTime->id,
                'result_date' => $currentMatchTime->result_date,
                'match_time' => $currentMatchTime->match_time,
            ] : null,
        ];
    }
}
