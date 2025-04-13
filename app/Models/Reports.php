<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reports extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'report_type',
        'generate_date',
        'report_file',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
