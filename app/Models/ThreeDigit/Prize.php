<?php

namespace App\Models\ThreeDigit;

use App\Jobs\WinnerPrizeCheck;
use App\Jobs\WinnerPrizeCheckUpdate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    use HasFactory;

    protected $table = 'prizes';

    protected $fillable = ['prize_one', 'prize_two'];

    // boot method
    protected static function booted()
    {
        static::created(function ($prize) {
            WinnerPrizeCheck::dispatch($prize);
            WinnerPrizeCheckUpdate::dispatch($prize);
        });
    }
}
