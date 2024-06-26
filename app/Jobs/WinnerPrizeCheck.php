<?php

namespace App\Jobs;

use App\Models\ThreeDigit\Lotto;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class WinnerPrizeCheck implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $prize;

    public function __construct($prize)
    {
        $this->prize = $prize;
    }

    public function handle(): void
    {
        if (! $this->isPlayingDay()) {
            return;
        }

        // Process winning entries directly for prize_one
        $this->processWinningEntries((string) $this->prize->prize_one);

        // Process winning entries directly for prize_two
        $this->processWinningEntries((string) $this->prize->prize_two);
    }

    protected function isPlayingDay(): bool
    {
        $playDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return in_array(Carbon::now()->englishDayOfWeek, $playDays);
    }

    protected function processWinningEntries($prizeNumber)
    {
        $today = Carbon::today();

        $winningEntries = DB::table('lottery_three_digit_copies')
            ->join('lottos', 'lottery_three_digit_copies.lotto_id', '=', 'lottos.id')
            ->where('lottery_three_digit_copies.bet_digit', $prizeNumber)
            ->where('lottery_three_digit_copies.prize_sent', 0)
            ->whereDate('lottery_three_digit_copies.created_at', $today)
            ->select('lottery_three_digit_copies.*')
            ->get();

        foreach ($winningEntries as $entry) {
            DB::transaction(function () use ($entry) {
                $lottery = Lotto::findOrFail($entry->lotto_id);
                $user = $lottery->user;
                $user->balance += $entry->sub_amount * 10; // Adjust based on your prize calculation
                $user->save();

                // Update the `prize_sent` flag
                $lottery->threedDigits()->updateExistingPivot($entry->three_digit_id, ['prize_sent' => 3]);
            });
        }
    }
}
