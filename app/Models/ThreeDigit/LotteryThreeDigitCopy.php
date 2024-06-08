<?php

namespace App\Models\ThreeDigit;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryThreeDigitCopy extends Model
{
    use HasFactory;

    protected $table = 'lottery_three_digit_copies';

    protected $fillable = ['threed_setting_id', 'lotto_id', 'three_digit_id', 'threed_match_time_id', 'user_id', 'bet_digit', 'sub_amount', 'prize_sent', 'match_status', 'res_date', 'res_time', 'match_start_date', 'result_number', 'win_lose', 'play_date', 'play_time', 'running_match'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
