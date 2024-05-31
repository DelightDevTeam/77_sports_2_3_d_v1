<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoDLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'two_d_limit',
    ];

    public function scopeLasted($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
