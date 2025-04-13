<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Feedbacks extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'prediction_id',
        'comment',
        'rating',
        'date'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function prediction()
    {
        return $this->belongsTo(PredictionResult::class, 'prediction_id');
    }


}
