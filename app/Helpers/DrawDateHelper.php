<?php

namespace App\Helpers;

use App\Models\ThreeDigit\ThreedSetting;
use Carbon\Carbon;

class DrawDateHelper
{
    /**
     * Get the current result date and match start date from the database.
     *
     * @return string
     */
    public static function getResultDate()
    {
        $resultDateSetting = ThreedSetting::where('status', 'open')->first();

        if ($resultDateSetting) {
            $matchStartDate = Carbon::createFromFormat('Y-m-d', $resultDateSetting->match_start_date)->format('Y-m-d');
            $resultDate = Carbon::createFromFormat('Y-m-d', $resultDateSetting->result_date)->format('Y-m-d');

            return [
                'match_start_date' => $matchStartDate,
                'result_date' => $resultDate,
            ];
        }

        // No open settings found, return null values
        return [
            'match_start_date' => null,
            'result_date' => null,
        ];
    }

    /**
     * Get Thai lottery draw dates in a specified range.
     *
     * @param  int  $startYear
     * @param  int  $endYear
     * @return array
     */
    public static function getThaiLotteryDrawDatesInRange($startYear, $endYear)
    {
        $drawDates = [];

        // Loop through each year
        for ($year = $startYear; $year <= $endYear; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                switch ($month) {
                    case 1:
                        // January only 16th
                        $drawDates[] = Carbon::createFromDate($year, 1, 16)->format('jS F Y');
                        break;
                    case 2:
                        // February 1st and 16th
                        $drawDates[] = Carbon::createFromDate($year, 2, 1)->format('jS F Y');
                        $drawDates[] = Carbon::createFromDate($year, 2, 16)->format('jS F Y');
                        break;
                    case 5:
                        // May 2nd and 16th
                        $drawDates[] = Carbon::createFromDate($year, 5, 2)->format('jS F Y');
                        $drawDates[] = Carbon::createFromDate($year, 5, 16)->format('jS F Y');
                        break;
                    case 12:
                        // December 1st, 16th, and 31st
                        $drawDates[] = Carbon::createFromDate($year, 12, 1)->format('jS F Y');
                        $drawDates[] = Carbon::createFromDate($year, 12, 16)->format('jS F Y');
                        $drawDates[] = Carbon::createFromDate($year, 12, 31)->format('jS F Y');
                        break;
                    default:
                        // Other months 1st and 16th
                        $drawDates[] = Carbon::createFromDate($year, $month, 1)->format('jS F Y');
                        $drawDates[] = Carbon::createFromDate($year, $month, 16)->format('jS F Y');
                        break;
                }
            }
        }

        return $drawDates;
    }
}
