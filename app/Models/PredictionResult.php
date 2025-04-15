<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredictionResult extends Model
{
    protected $fillable = [
        'record_id',
        'prediction_date',
        'predicted_score',
        'recommendation'
    ];

    public function academicRecord()
    {
        return $this->belongsTo(AcademicRecord::class, 'record_id');
    }

    public function student()
    {
        return $this->hasOneThrough(
            Student::class,
            AcademicRecord::class,
            'id',                 // academic_records.id
            'id',                 // students.id
            'record_id',          // prediction_results.record_id
            'student_id'          // academic_records.student_id
        );
    }
}
