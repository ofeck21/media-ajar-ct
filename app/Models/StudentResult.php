<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_session_id',
        'student_id',
        'student_group_id',
        'diagnostic_score',
        'diagnostic_level',
        'total_stars',
        'total_steps',
        'total_attempts',
        'status',
        'reflections'
    ];

    protected $casts = [
        'reflections' => 'array',
    ];

    public function gameSession()
    {
        return $this->belongsTo(GameSession::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}
