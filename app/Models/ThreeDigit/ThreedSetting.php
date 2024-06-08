<?php

namespace App\Models\ThreeDigit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreedSetting extends Model
{
    use HasFactory;

    protected $fillable = ['result_date', 'result_time', 'result_number', 'status', 'admin_log', 'user_log', 'match_start_date', 'endpoint'];
}
