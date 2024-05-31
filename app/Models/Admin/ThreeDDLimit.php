<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThreeDDLimit extends Model
{
    use HasFactory;

    protected $fillable = [
        'three_d_limit',
    ];

    public function scopeLasted($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
