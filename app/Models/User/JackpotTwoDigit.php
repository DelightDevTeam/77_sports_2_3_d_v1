<?php

namespace App\Models\User;

use App\Models\User\JackpotTwoDigitCopy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class JackpotTwoDigit extends Model
{
    use HasFactory;

    protected $table = 'jackpot_two_digit';

    protected $fillable = ['jackpot_id', 'two_digit_id', 'sub_amount', 'prize_sent'];

    protected static function booted()
    {
        static::created(function ($jackpotTwoDigit) {
            Log::info('Inside JackpotTwoDigit created event');
            try {
                JackpotTwoDigitCopy::create([
                    'jackpot_id' => $jackpotTwoDigit->jackpot_id,
                    'two_digit_id' => $jackpotTwoDigit->two_digit_id,
                    'sub_amount' => $jackpotTwoDigit->sub_amount,
                    'prize_sent' => $jackpotTwoDigit->prize_sent,
                ]);
                Log::info('JackpotTwoDigitCopy created successfully');
            } catch (\Exception $e) {
                Log::error('Error in JackpotTwoDigit created event: '.$e->getMessage());
            }
        });

    }
}
