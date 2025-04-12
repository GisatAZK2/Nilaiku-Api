<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'age', 'gender', 'education', 'is_guest', 'guest_session_token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
