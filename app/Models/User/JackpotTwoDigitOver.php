<?php

namespace App\Models\User;

use App\Models\User\JackpotTwoDigitOverCopy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JackpotTwoDigitOver extends Model
{
    use HasFactory;

    protected $table = 'jackpot_over';

    protected $fillable = ['jackpot_id', 'two_digit_id', 'sub_amount', 'prize_sent'];

    protected static function booted()
    {
        static::created(function ($pivot) {
            JackpotTwoDigitOverCopy::create($pivot->toArray());
        });
    }
}
