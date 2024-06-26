<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\TwoD\Lottery;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class CheckForEveningWinners implements ShouldQueue
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
        // $today = Carbon::today();

        // if ($this->twodWiner->session !== 'evening') {
        //     Log::info('This TwodWiner is not for the evening session, exiting.');
        //     return;
        // }
        Log::info('Job started for Evening TwodWiner with prize_no: ' . $this->twodWiner->prize_no);

        $today = Carbon::today();
        //\Log::info("Today's date: " . $today->toDateString());
        $playDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        if (! in_array(strtolower(date('l')), $playDays)) {
            return; // exit if it's not a playing day
        }
        if ($this->twodWiner->session !== 'morning') {
            Log::info('This TwodWiner is not for the Evening session, exiting.');
            return;
        }

        // Use bet_digit instead of calculating two_digit_id
        $bet_digit = $this->twodWiner->prize_no;

        $winningEntries = DB::table('lottery_two_digit_copies')
            ->join('lotteries', 'lottery_two_digit_copies.lottery_id', '=', 'lotteries.id')
            ->where('lottery_two_digit_copies.bet_digit', $bet_digit) // Use the bet_digit
            ->where('lottery_two_digit_copies.prize_sent', false)
            ->whereDate('lottery_two_digit_copies.created_at', $today)
            ->select('lottery_two_digit_copies.*')
            ->get();

        if ($winningEntries->isEmpty()) {
            Log::info('No winning entries found.');
        } else {
            Log::info('Processing ' . $winningEntries->count() . ' winning entries.');
        }

        foreach ($winningEntries as $entry) {
            DB::transaction(function () use ($entry) {
                $lottery = Lottery::findOrFail($entry->lottery_id);
                $user = $lottery->user;
                $user->balance += $entry->sub_amount * 85; // Assuming the prize multiplier is 85
                $user->save();

                // Update prize_sent to true for the winning entry
                DB::table('lottery_two_digit_copies')
                    ->where('id', $entry->id)
                    ->update(['prize_sent' => true]);
            });
        }
    }

    // public function handle()
    // {
    //     //\Log::info('Job started for TwodWiner with prize_no: ' . $this->twodWiner->prize_no);

    //     $today = Carbon::today();
    //     //\Log::info("Today's date: " . $today->toDateString());

    //     if ($this->twodWiner->session !== 'evening') {
    //         Log::info('This TwodWiner is not for the evening session, exiting.');
    //         return;
    //     }

    //     // Convert prize_no to two_digit_id
    //     $two_digit_id = $this->twodWiner->prize_no === '00' ? 1 : intval($this->twodWiner->prize_no, 10) + 1;

    //     $winningEntries = DB::table('lottery_two_digit_copies')
    //         ->join('lotteries', 'lottery_two_digit_copies.lottery_id', '=', 'lotteries.id')
    //         ->where('lottery_two_digit_copies.two_digit_id', $two_digit_id) // Use the calculated two_digit_id
    //         ->where('lottery_two_digit_copies.prize_sent', false)
    //         ->whereDate('lottery_two_digit_copies.created_at', $today)
    //         ->select('lottery_two_digit_copies.*')
    //         ->get();

    //     if ($winningEntries->isEmpty()) {
    //         Log::info('No winning entries found.');
    //     } else {
    //         Log::info('Processing ' . $winningEntries->count() . ' winning entries.');
    //     }

    //     foreach ($winningEntries as $entry) {
    //         DB::transaction(function () use ($entry) {
    //             $lottery = Lottery::findOrFail($entry->lottery_id);
    //             $user = $lottery->user;
    //             $user->balance += $entry->sub_amount * 85; // Assuming the prize multiplier is 85
    //             $user->save();

    //             // Update prize_sent to true for the winning entry
    //             $lottery->twoDigits()->updateExistingPivot($entry->two_digit_id, ['prize_sent' => true]);
    //         });
    //     }
    // }
}
