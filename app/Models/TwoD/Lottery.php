<?php

namespace App\Models\TwoD;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lottery extends Model
{
    use HasFactory;

    protected $fillable = [
        'pay_amount',
        'total_amount',
        'user_id',
        'session',
        'slip_no',
        'lottery_match_id',
        'comission',
        'commission_amount',
    ];

    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
