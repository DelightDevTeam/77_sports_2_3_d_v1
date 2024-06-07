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

class MorningUpdatePrizeSent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $twodWiner;

    public function __construct($twodWiner)
    {
        $this->twodWiner = $twodWiner;
    }

    public function handle()
    {
        //\Log::info('Job started for TwodWiner with prize_no: ' . $this->twodWiner->prize_no);

        $today = Carbon::today();
        //\Log::info("Today's date: " . $today->toDateString());
        $playDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'];
        if (! in_array(strtolower(date('l')), $playDays)) {
            return; // exit if it's not a playing day
        }
        if ($this->twodWiner->session !== 'morning') {
            //  \Log::info('This TwodWiner is not for the evening session, exiting.');
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
            Log::info('Prize_Sent - No winning entries found.');
        } else {
            Log::info('Prize_Sent - Processing '.$winningEntries->count().' winning entries.');
        }

        foreach ($winningEntries as $entry) {
            DB::transaction(function () use ($entry) {
                $lottery = Lottery::findOrFail($entry->lottery_id);
                // $user = $lottery->user;
                // $user->balance += $entry->sub_amount * 85;
                // $user->save();

                // Update prize_sent to true for the winning entry
                $lottery->twoDigits()->updateExistingPivot($entry->two_digit_id, ['prize_sent' => true]);
            });
        }
    }

    // public function handle()
    // {
    //     Log::info('Updated prize sent Job started for TwodWiner with prize_no: ' . $this->twodWiner->prize_no);

    //     $today = Carbon::today();
    //     Log::info("Today's date: " . $today->toDateString());

    //     if ($this->twodWiner->session !== 'morning') {
    //         Log::info('This TwodWiner is not for the morning session, exiting.');
    //         return;
    //     }

    //     // Convert prize_no to two_digit_id
    //     $two_digit_id = $this->twodWiner->prize_no === '00' ? 1 : intval($this->twodWiner->prize_no, 10) + 1;
    //     Log::info("Calculated two_digit_id: " . $two_digit_id);

    //     $winningEntries = DB::table('lottery_two_digit_copies')
    //         ->join('lotteries', 'lottery_two_digit_copies.lottery_id', '=', 'lotteries.id')
    //         ->where('lottery_two_digit_copies.two_digit_id', $two_digit_id)
    //         ->where('lottery_two_digit_copies.prize_sent', false)
    //         ->whereDate('lottery_two_digit_copies.created_at', $today)
    //         ->select('lottery_two_digit_copies.*')
    //         ->get();

    //     Log::info('Number of winning entries found: ' . $winningEntries->count());

    //     if ($winningEntries->isEmpty()) {
    //         Log::info('Update Prize Sent No winning entries found.');
    //     } else {
    //         Log::info('Update Prize Sent Processing ' . $winningEntries->count() . ' winning entries.');

    //         foreach ($winningEntries as $entry) {
    //             DB::transaction(function () use ($entry) {
    //                 $lottery = Lottery::findOrFail($entry->lottery_id);
    //                 // Update prize_sent to true for the winning entry
    //                 $lottery->twoDigits()->updateExistingPivot($entry->two_digit_id, ['prize_sent' => true]);
    //                 Log::info('Updated prize_sent for lottery_id: ' . $entry->lottery_id . ', two_digit_id: ' . $entry->two_digit_id);
    //             });
    //         }
    //     }
    // }
}
