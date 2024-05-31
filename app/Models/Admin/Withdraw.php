<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kpay_no',
        'cbpay_no',
        'wavepay_no',
        'ayapay_no',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
