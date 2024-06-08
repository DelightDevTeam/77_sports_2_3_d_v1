<?php

namespace App\Models\ThreeDigit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreedMatchTime extends Model
{
    use HasFactory;

    protected $fillable = [
        'result_date',
        'result_time',
        'match_time',
        //'run_match',
        'status',
    ];
}
