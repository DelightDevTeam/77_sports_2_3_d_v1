<?php

namespace App\Models\ThreeDigit;

use App\Jobs\CheckForThreeDWinners;
use App\Jobs\ThreeDPermutationPrizeSent;
use App\Jobs\ThreeDPermutationUpdatePrizeSent;
use App\Jobs\ThreeDUpdatePrizeSent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreeWinner extends Model
{
    use HasFactory;

    protected $table = 'three_winners';

    protected $fillable = ['prize_no'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    protected static function booted()
    {
        static::created(function ($threedWinner) {
            //if ($threedWinner->prize_no) {
            CheckForThreeDWinners::dispatch($threedWinner);
            ThreeDPermutationPrizeSent::dispatch($threedWinner);
            ThreeDUpdatePrizeSent::dispatch($threedWinner);
            ThreeDPermutationUpdatePrizeSent::dispatch($threedWinner);
            //}

        });
    }
}
