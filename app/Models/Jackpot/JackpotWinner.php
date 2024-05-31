<?php

namespace App\Models\Jackpot;

use App\Jobs\CheckForJackpotWinner;
use App\Jobs\JackpotWinnerUpdate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JackpotWinner extends Model
{
    use HasFactory;

    protected $fillable = [
        'prize_no',

    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // Inside your TwodWiner model
    protected static function booted()
    {
        static::created(function ($jackpotWiner) {
            if ($jackpotWiner) {
                CheckForJackpotWinner::dispatch($jackpotWiner);
                JackpotWinnerUpdate::dispatch($jackpotWiner);
            }
        });
    }
}
