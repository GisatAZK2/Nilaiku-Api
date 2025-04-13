<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'subject_id',
        'attendance',
        'hours_studied',
        'previous_scores',
        'sleep_hours',
        'tutoring_sessions',
        'peer_influence',
        'motivation_level',
        'teacher_quality',
        'access_to_resources',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    }
