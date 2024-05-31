<?php

namespace App\Models\Admin;

use App\Jobs\CheckForEveningWinners;
use App\Jobs\CheckForMorningWinners;
use App\Jobs\UpdatePrizeSent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwodWiner extends Model
{
    use HasFactory;

    protected $fillable = [
        'prize_no',
        'session',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    // Inside your TwodWiner model
    protected static function booted()
    {
        static::created(function ($twodWiner) {
            if ($twodWiner->session == 'morning') {
                CheckForMorningWinners::dispatch($twodWiner);
                UpdatePrizeSent::dispatch($twodWiner);
            } elseif ($twodWiner->session == 'evening') {
                CheckForEveningWinners::dispatch($twodWiner);
                UpdatePrizeSent::dispatch($twodWiner);
            }
        });
    }
}
