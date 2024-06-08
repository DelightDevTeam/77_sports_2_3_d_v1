<?php

namespace App\Models\ThreeDigit;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin\LotteryMatch;
use Illuminate\Database\Eloquent\Model;
use App\Models\ThreeDigit\ThreedMatchTime;
use App\Models\ThreeDigit\LotteryThreeDigitPivot;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lotto extends Model
{
    use HasFactory;

    protected $fillable = [
        'total_amount',
        'user_id',
        'lottery_match_id',
        'comission',
        'commission_amount',
        'status',
        'slip_no',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lotteryThreeDigitPivots()
    {
        return $this->hasMany(LotteryThreeDigitPivot::class, 'lotto_id');
    }

    public function lotteryMatch()
    {
        return $this->belongsTo(LotteryMatch::class, 'lottery_match_id');
    }

    public function threedMatchTime()
    {
        // Assuming you have a model called ThreedMatchTime and there is a 'lottery_match_id' foreign key in it.
        return $this->hasOne(ThreedMatchTime::class, 'id', 'lottery_match_id');
    }

    public function threedDigits()
    {
        return $this->belongsToMany(ThreeDigit::class, 'lottery_three_digit_copies')->withPivot('sub_amount', 'prize_sent')->withTimestamps();
    }

    public function DisplayThreeDigits()
    {
        return $this->belongsToMany(ThreeDigit::class, 'lottery_three_digit_pivots', 'lotto_id', 'three_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at');
    }

    public function displayThreeDigitsOneWeekHistory($jackpotIds = [])
    {
        // If no specific jackpot IDs are provided, fetch all jackpot IDs
        if (empty($digitIds)) {
            $digitIds = Lotto::pluck('id');
        }
        // Define your date ranges using Carbon
        $startDateFirstRange = Carbon::now()->startOfMonth();
        $endDateFirstRange = Carbon::now()->startOfMonth()->addDays(16);
        $startDateSecondRange = Carbon::now()->startOfMonth()->addDays(17);
        $endDateSecondRange = Carbon::now()->endOfMonth();

        return $this->belongsToMany(ThreeDigit::class, 'lottery_three_digit_pivots', 'lotto_id', 'three_digit_id')
            ->select([
                'three_digits.*',
                'lottery_three_digit_pivots.lotto_id AS pivot_lotto_id',
                'lottery_three_digit_pivots.three_digit_id AS pivot_three_digit_id',
                'lottery_three_digit_pivots.bet_digit',
                'lottery_three_digit_pivots.sub_amount AS pivot_sub_amount',
                'lottery_three_digit_pivots.prize_sent AS pivot_prize_sent',
                'lottery_three_digit_pivots.play_date',
                'lottery_three_digit_pivots.play_time',
                'lottery_three_digit_pivots.created_at AS pivot_created_at',
                'lottery_three_digit_pivots.updated_at AS pivot_updated_at',
            ])
            ->where(function ($query) use ($startDateFirstRange, $endDateFirstRange, $startDateSecondRange, $endDateSecondRange) {
                $query->whereBetween('lottery_three_digit_pivots.created_at', [$startDateFirstRange, $endDateFirstRange])
                    ->orWhereBetween('lottery_three_digit_pivots.created_at', [$startDateSecondRange, $endDateSecondRange]);
            })
            ->whereIn('lottery_three_digit_pivots.lotto_id', $digitIds)
            ->orderBy('lottery_three_digit_pivots.created_at', 'desc');
    }

    // three digit once month  history

    public function displayThreeDigitsOneMonthHistory($jackpotIds = [])
    {
        // If no specific jackpot IDs are provided, fetch all jackpot IDs
        if (empty($jackpotIds)) {
            $jackpotIds = Lotto::pluck('id');
        }

        // Define your date ranges using Carbon
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonthNoOverflow()->startOfMonth()->addDay(); // This will give you the second day of the next month

        return $this->belongsToMany(ThreeDigit::class, 'lottery_three_digit_pivots', 'lotto_id', 'three_digit_id')
            ->select([
                'three_digits.*',
                'lottery_three_digit_pivots.lotto_id AS pivot_lotto_id',
                'lottery_three_digit_pivots.three_digit_id AS pivot_three_digit_id',
                'lottery_three_digit_pivots.sub_amount AS pivot_sub_amount',
                'lottery_three_digit_pivots.prize_sent AS pivot_prize_sent',
                'lottery_three_digit_pivots.created_at AS pivot_created_at',
                'lottery_three_digit_pivots.updated_at AS pivot_updated_at',
            ])
            ->whereBetween('lottery_three_digit_pivots.created_at', [$startDate, $endDate])
            ->whereIn('lottery_three_digit_pivots.lotto_id', $jackpotIds)
            ->orderBy('lottery_three_digit_pivots.created_at', 'desc');
    }

    // three digit once month history
    public function DisplayThreeDigitsOnceMonth()
    {
        $onceMonthStart = Carbon::now()->startOfMonth();
        $onceMonthEnd = Carbon::now()->endOfMonth();

        return $this->belongsToMany(ThreeDigit::class, 'lottery_three_digit_pivots', 'lotto_id', 'three_digit_id')->withPivot('bet_digit', 'sub_amount', 'prize_sent', 'play_date', 'play_time', 'created_at')
            ->wherePivotBetween('created_at', [$onceMonthStart, $onceMonthEnd]);
    }
}
