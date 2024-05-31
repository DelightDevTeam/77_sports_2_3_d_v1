<?php

namespace App\Models;

use App\Models\Admin\LotteryMatch;
use App\Models\Admin\PrizeSentTwoDigit;
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

    // two digit early morning
    public function twoDigitsEarlyMorning()
    {
        $morningStart = Carbon::now()->startOfDay()->addHours(6);
        $morningEnd = Carbon::now()->startOfDay()->addHours(10);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    public function twoDigitsMorning()
    {
        $morningStart = Carbon::now()->startOfDay()->setTime(5, 0);
        $morningEnd = Carbon::now()->startOfDay()->setTime(12, 0);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    }

    // two digit early evening
    public function twoDigitsEarlyEvening()
    {
        $eveningStart = Carbon::now()->startOfDay()->addHours(12);
        $eveningEnd = Carbon::now()->startOfDay()->addHours(14);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$eveningStart, $eveningEnd]);
    }

    public function twoDigitsEvening()
    {
        $eveningStart = Carbon::now()->startOfDay()->addHours(12);
        $eveningEnd = Carbon::now()->startOfDay()->addHours(24);

        return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivots', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
            ->wherePivotBetween('created_at', [$eveningStart, $eveningEnd]);
    }

    // public function twoDigitsMorning()
    // {
    //     $morningStart = Carbon::now()->startOfDay()->addHours(6);
    //     $morningEnd = Carbon::now()->startOfDay()->addHours(12);
    //     return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivot', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
    //                 ->wherePivotBetween('created_at', [$morningStart, $morningEnd]);
    // }

    // public function twoDigitsEvening()
    // {
    //     $eveningStart = Carbon::now()->startOfDay()->addHours(12);
    //     $eveningEnd = Carbon::now()->startOfDay()->addHours(24);
    //     return $this->belongsToMany(TwoDigit::class, 'lottery_two_digit_pivot', 'lottery_id', 'two_digit_id')->withPivot('sub_amount', 'prize_sent', 'created_at')
    //                 ->wherePivotBetween('created_at', [$eveningStart, $eveningEnd]);
    // }
}
