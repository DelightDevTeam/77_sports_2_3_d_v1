<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use App\Models\ThreeDigit\ThreedMatchTime;

class ThreedMatchTimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startYear = 2024; // Starting year
        $endYear = 2031;   // End year

        for ($year = $startYear; $year <= $endYear; $year++) {
            $matchCounter = 1;

            for ($month = 1; $month <= 12; $month++) {
                if ($month == 1) {
                    // January: one match on the 16th
                    $this->createMatchTime($year, 1, 16, $matchCounter);
                    $matchCounter++;
                } elseif ($month == 5) {
                    // May: first match on the 2nd, second match on the 16th
                    $this->createMatchTime($year, 5, 2, $matchCounter);
                    $matchCounter++;
                    $this->createMatchTime($year, 5, 16, $matchCounter);
                    $matchCounter++;
                } elseif ($month == 12) {
                    // December: first match on the 1st, second match on the 16th, third match on the 31st
                    $this->createMatchTime($year, 12, 1, $matchCounter);
                    $matchCounter++;
                    $this->createMatchTime($year, 12, 16, $matchCounter);
                    $matchCounter++;
                    $this->createMatchTime($year, 12, 31, $matchCounter);
                    $matchCounter++;
                } else {
                    // February to November: two matches, on the 1st and the 16th
                    $this->createMatchTime($year, $month, 1, $matchCounter);
                    $matchCounter++;
                    $this->createMatchTime($year, $month, 16, $matchCounter);
                    $matchCounter++;
                }
            }
        }
    }

    private function createMatchTime(int $year, int $month, int $day, int $matchCounter): void
    {
        $resultDate = Carbon::createFromDate($year, $month, $day);
        $matchDescription = "{$year}-".$resultDate->format('m').'-'.$this->ordinal($matchCounter);

        ThreedMatchTime::create([
            'result_date' => $resultDate->format('Y-m-d'),
            'result_time' => '15:30:00',
            'match_time' => $matchDescription,
            'status' => 'closed',
        ]);

    }

    // private function createMatchTime(int $year, int $month, int $day, int $matchCounter): void
    // {
    //     $resultDate = Carbon::createFromDate($year, $month, $day);
    //     $matchDescription = "{$year}-".$resultDate->format('F').'-'.$this->ordinal($matchCounter).'-3D-Match';

    //     ThreedMatchTime::create([
    //         'result_date' => $resultDate->format('Y-m-d'),
    //         'result_time' => '15:30:00',
    //         'match_time' => $matchDescription,
    //         'run_match' => $i,
    //         'status' => 'closed',
    //     ]);
    // }

    private function ordinal(int $number): string
    {
        $suffixes = ['th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th'];
        if ((($number % 100) >= 11) && (($number % 100) <= 13)) {
            return $number.'th';
        }

        return $number.$suffixes[$number % 10];
    }

    private function MatchName(int $num)
    {
        $match_name = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24'];

        return $match_name[$num];
    }
}
