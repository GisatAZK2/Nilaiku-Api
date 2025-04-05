<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class 
PredictionResult extends Model
{
    protected $fillable = [
        'record_id',
        'prediction_date',
        'prediction_score',
        'recommendation'
    ];

    protected $cast = [
        'prediction_date' => 'datetime',
    ];

    public function academicRecord()
    {
        return $this->belongsTo(AcademicRecord::class);
    }
}
