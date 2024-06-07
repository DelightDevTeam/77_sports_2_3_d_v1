<?php

namespace App\Models\TwoD;

use App\Models\Admin\LotteryMatch;
use App\Models\Admin\TwoDigit;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    use HasFactory;

    protected $fillable = [
        'pay_amount',
        'total_amount',
        'user_id',
        'session',
        'slip_no',
        'lottery_match_id',
        'comission',
        'commission_amount',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lotteryMatch()
    {
        return $this->belongsTo(LotteryMatch::class, 'lottery_match_id');
    }

    public function twoDigits()
    {
        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots')->withPivot('sub_amount', 'prize_sent', 'win_lose')->withTimestamps();
    }

    public function twoDigitCopies()
    {
        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_copies')->withPivot('sub_amount', 'prize_sent', 'win_lose')->withTimestamps();
    }

    public function twoDigitsMorning()
    {
        $morningStart = Carbon::now()->startOfDay()->setTime(5, 0);
        $morningEnd = Carbon::now()->startOfDay()->setTime(12, 0);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'win_lose', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    public function twoDigitsForSession()
    {
        $morningStart = Carbon::now()->startOfDay()->setTime(5, 30);
        $morningEnd = Carbon::now()->startOfDay()->setTime(12, 15);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots')
            ->withPivot('sub_amount', 'prize_sent', 'play_date', 'play_time', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    public function twoDigitsOnceMonth()
    {
        // Set the time zone to Myanmar Time
        $timezone = '+06:30';

        // Define start and end of the current month with the desired time zone
        $onceMonthStart = Carbon::now($timezone)->startOfMonth();
        $onceMonthEnd = Carbon::now($timezone)->endOfMonth();

        //dd($onceMonthStart);
        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots', 'lottery_id', 'two_digit_id')
            ->withPivot('bet_digit', 'sub_amount', 'prize_sent', 'play_date', 'play_time', 'created_at')
            ->wherePivotBetween('created_at', [$onceMonthStart, $onceMonthEnd]);
    }

    public function dailyMorningHistoryForAdmin($startTime, $endTime)
    {
        // Define your date ranges using Carbon
        $startDate = Carbon::createFromFormat('H:i', $startTime);
        $endDate = Carbon::createFromFormat('H:i', $endTime);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots', 'lottery_id', 'two_digit_id')
            ->select([
                'two_digits.*',
                'lottery_two_digit_pivots.lottery_id AS pivot_lottery_id',
                'lottery_two_digit_pivots.two_digit_id AS pivot_two_digit_id',
                'lottery_two_digit_pivots.sub_amount AS pivot_sub_amount',
                'lottery_two_digit_pivots.prize_sent AS pivot_prize_sent',
                'lottery_two_digit_pivots.created_at AS pivot_created_at',
                'lottery_two_digit_pivots.updated_at AS pivot_updated_at',
            ])
            ->whereBetween('lottery_two_digit_pivots.created_at', [$startDate, $endDate])
            ->orderBy('lottery_two_digit_pivots.created_at', 'desc');
    }

    public function dailyEveningHistoryForAdmin($startTime, $endTime)
    {
        // Define your date ranges using Carbon
        $startDate = $startTime->format('H:i');
        $endDate = $endTime->format('H:i');

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots', 'lottery_id', 'two_digit_id')
            ->select([
                'two_digits.*',
                'lottery_two_digit_pivots.lottery_id AS pivot_lottery_id',
                'lottery_two_digit_pivots.two_digit_id AS pivot_two_digit_id',
                'lottery_two_digit_pivots.sub_amount AS pivot_sub_amount',
                'lottery_two_digit_pivots.prize_sent AS pivot_prize_sent',
                'lottery_two_digit_pivots.created_at AS pivot_created_at',
                'lottery_two_digit_pivots.updated_at AS pivot_updated_at',
            ])
            ->whereBetween('lottery_two_digit_pivots.created_at', [$startDate, $endDate])
            ->orderBy('lottery_two_digit_pivots.created_at', 'desc');
    }

    public function twoDigitsEvening()
    {
        $eveningStart = Carbon::now()->startOfDay()->addHours(12);
        $eveningEnd = Carbon::now()->startOfDay()->addHours(24);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$eveningStart, $eveningEnd]);
    }
}
