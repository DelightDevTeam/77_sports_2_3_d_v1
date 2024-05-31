<?php

namespace App\Models\Admin;

use App\Models\Admin\BetLottery;
use App\Models\Admin\Lottery;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotteryMatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'match_name',
        'is_active',
    ];

    public function lotteries()
    {
        return $this->hasMany(Lottery::class);
    }

    public function betLotteries()
    {
        return $this->hasMany(BetLottery::class);
    }

    public function threedMatchTime()
    {
        return $this->hasOne(ThreedMatchTime::class, 'id', 'lottery_match_id');
    }
}
