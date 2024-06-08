<?php

namespace Database\Seeders;

use App\Models\ThreeDigit\ThreedSetting;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThreedSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startYear = 2024; // Starting year
        $endYear = 2031;   // 20 years from 2024 to 2044

        // Loop through each year
        for ($year = $startYear; $year <= $endYear; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                // Match start date logic for the first game
                $matchStartDateFirst = $month == 1
                    ? Carbon::createFromDate($year - 1, 12, 17)  // December 17th of the previous year
                    : Carbon::createFromDate($year, $month - 1, 17); // 17th of the previous month

                $resultDateFirst = Carbon::createFromDate($year, $month, 1);

                ThreedSetting::create([
                    'result_date' => $resultDateFirst->format('Y-m-d'),
                    'result_time' => '15:30:00',
                    'match_start_date' => $matchStartDateFirst->format('Y-m-d'),
                    'status' => 'closed',
                    'endpoint' => 'https://77sportsmm.com',
                ]);

                // Match start date and result date logic for the second game
                $matchStartDateSecond = Carbon::createFromDate($year, $month, 2); // 2nd of the same month
                $resultDateSecond = Carbon::createFromDate($year, $month, 16); // 16th of the same month

                ThreedSetting::create([
                    'result_date' => $resultDateSecond->format('Y-m-d'),
                    'result_time' => '15:30:00',
                    'match_start_date' => $matchStartDateSecond->format('Y-m-d'),
                    'status' => 'closed',
                    'endpoint' => 'https://77sportsmm.com',
                ]);

                // Special case for May
                if ($month == 5) {
                    // First game in May
                    $resultDateMay2 = Carbon::createFromDate($year, 5, 2);
                    $matchStartDateApril17 = Carbon::createFromDate($year - 1, 4, 17); // 17th of April

                    ThreedSetting::create([
                        'result_date' => $resultDateMay2->format('Y-m-d'),
                        'result_time' => '15:30:00',
                        'match_start_date' => $matchStartDateApril17->format('Y-m-d'),
                        'status' => 'closed',
                        'endpoint' => 'https://77sportsmm.com',
                    ]);

                    // Second game in May
                    $resultDateMay16 = Carbon::createFromDate($year, 5, 16);
                    $matchStartDateMay2 = Carbon::createFromDate($year, 5, 3);

                    ThreedSetting::create([
                        'result_date' => $resultDateMay16->format('Y-m-d'),
                        'result_time' => '15:30:00',
                        'match_start_date' => $matchStartDateMay2->format('Y-m-d'),
                        'status' => 'closed',
                        'endpoint' => 'https://77sportsmm.com',
                    ]);
                }

                // Special case for December 31st (end of the year)
                if ($month == 12) {
                    $resultDate31 = Carbon::createFromDate($year, 12, 31);
                    $matchStartDate17 = Carbon::createFromDate($year, 12, 17);

                    ThreedSetting::create([
                        'result_date' => $resultDate31->format('Y-m-d'),
                        'result_time' => '15:30:00',
                        'match_start_date' => $matchStartDate17->format('Y-m-d'),
                        'status' => 'closed',
                        'endpoint' => 'https://77sportsmm.com',
                    ]);
                }
            }
        }
    }
}
