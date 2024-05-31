<?php

namespace App\Models\TwoD;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryTwoDigitCopy extends Model
{
    use HasFactory;

    protected $table = 'lottery_two_digit_copies';

    protected $fillable = [
        'lottery_id',
        'twod_setting_id',
        'two_digit_id',
        'user_id',
        'bet_digit',
        'sub_amount',
        'prize_sent',
        'match_status',
        'res_date',
        'res_time',
        'session',
        'admin_log',
        'user_log',
        'play_date',
        'play_time',
        'win_lose',
    ];

    // protected $primaryKey = 'id';

    protected $guarded = ['id'];

    public function lottery()
    {
        return $this->belongsTo(Lottery::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
