<?php

namespace Database\Seeders;

use App\Models\TwoD\TwodSetting;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TwodSettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Set the starting date to today's date
        $currentDate = Carbon::now();

        // Iterate over the next 10 years
        for ($year = 0; $year < 5; $year++) {
            // Iterate over each month in the year
            for ($month = 1; $month <= 12; $month++) {
                // Determine the number of days in the month
                $daysInMonth = Carbon::create($currentDate->year + $year, $month)->daysInMonth;

                // Iterate over each day in the month
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    // Calculate the date
                    $date = Carbon::create($currentDate->year + $year, $month, $day);

                    // Set status to 'open' for today's sessions, 'closed' otherwise
                    $morningStatus = $date->isToday() ? 'open' : 'closed';
                    $eveningStatus = $date->isToday() ? 'open' : 'closed';

                    // Morning session
                    TwodSetting::create([
                        'result_date' => $date->format('Y-m-d'),
                        'result_time' => '12:01:00', // Morning open time
                        'session' => 'morning',
                        'status' => $morningStatus,
                    ]);

                    // Evening session
                    TwodSetting::create([
                        'result_date' => $date->format('Y-m-d'),
                        'result_time' => '16:30:00', // Evening open time
                        'session' => 'evening',
                        'status' => $eveningStatus,
                    ]);
                }
            }
        }
    }
}
