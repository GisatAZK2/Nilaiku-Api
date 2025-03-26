<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredictionResult extends Model
{
    protected $guard = ['id'];

    protected $cast = [
        'prediction_date' => 'datetime',
    ];

    public function academicRecord()
    {
        return $this->belongsTo(AcademicRecord::class);
    }
}
