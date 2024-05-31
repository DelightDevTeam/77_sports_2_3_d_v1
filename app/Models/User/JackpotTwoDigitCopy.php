<?php

namespace App\Models\User;

use App\Models\User\JackpotTwoDigit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JackpotTwoDigitCopy extends Model
{
    use HasFactory;

    protected $table = 'jackpot_two_digit_copy';

    protected $fillable = ['jackpot_id', 'two_digit_id', 'sub_amount', 'prize_sent'];
}
