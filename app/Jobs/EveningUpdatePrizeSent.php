<?php

namespace App\Jobs;

use App\Models\TwoD\Lottery;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EveningUpdatePrizeSent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $twodWiner;

    public function __construct($twodWiner)
    {
        $this->twodWiner = $twodWiner;
    }

    public function handle()
    {
        Log::info('Updated prize sent Job started for TwodWiner with prize_no: '.$this->twodWiner->prize_no);

        $today = Carbon::today();
        //\Log::info("Today's date: " . $today->toDateString());

        if ($this->twodWiner->session !== 'evening') {
            Log::info('This TwodWiner is not for the evening session, exiting.');

            return;
        }

        // Convert prize_no to two_digit_id
        $two_digit_id = $this->twodWiner->prize_no === '00' ? 1 : intval($this->twodWiner->prize_no, 10) + 1;

        $winningEntries = DB::table('lottery_two_digit_pivots')
            ->join('lotteries', 'lottery_two_digit_pivots.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_pivots.two_digit_id', $two_digit_id) // Use the calculated two_digit_id
            ->where('lottery_two_digit_pivots.prize_sent', false)
            ->whereDate('lottery_two_digit_pivots.created_at', $today)
            ->select('lottery_two_digit_pivots.*')
            ->get();

        if ($winningEntries->isEmpty()) {
            Log::info('Update Prize Sent No winning entries found.');
        } else {
            Log::info('Update Prize Sent Processing '.$winningEntries->count().' winning entries.');
        }

        foreach ($winningEntries as $entry) {
            DB::transaction(function () use ($entry) {
                $lottery = Lottery::findOrFail($entry->lottery_id);
                // Update prize_sent to true for the winning entry
                $lottery->twoDigits()->updateExistingPivot($entry->two_digit_id, ['prize_sent' => true]);
            });
        }
    }

    //     public function handle()
    // {
    //     // Check if today is a playing day
    //     $today = Carbon::today();
    //     $playDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
    //     if (!in_array(strtolower(date('l')), $playDays)) {
    //         return; // exit if it's not a playing day
    //     }

    //     // Find all winning entries using raw SQL
    //     $winningEntries = DB::table('lottery_two_digit_pivot')
    //         ->join('lotteries', 'lottery_two_digit_pivot.lottery_id', '=', 'lotteries.id')
    //         ->whereRaw('lottery_two_digit_pivot.two_digit_id = ?', [$this->twodWiner->prize_no])
    //         ->whereRaw('lottery_two_digit_pivot.prize_sent = 0')
    //         ->whereRaw('DATE(lottery_two_digit_pivot.created_at) = ?', [$today])
    //         ->select('lottery_two_digit_pivot.*') // Select all columns from pivot table
    //         ->get();

    //     foreach ($winningEntries as $entry) {
    //         DB::transaction(function () use ($entry) {
    //             // Retrieve the lottery for this entry
    //             $lottery = Lottery::findOrFail($entry->lottery_id);
    //             $methodToUpdatePivot = 'twoDigits';
    //             // Update prize_sent in pivot
    //             $lottery->$methodToUpdatePivot()->updateExistingPivot($entry->two_digit_id, ['prize_sent' => 1]);
    //         });
    //     }
    // }
}
