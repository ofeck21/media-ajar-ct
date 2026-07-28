<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $table = 'student_groups';

    protected $fillable = ['game_session_id', 'name', 'type'];

    public function gameSession()
    {
        return $this->belongsTo(GameSession::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'group_student', 'student_group_id', 'student_id');
    }
}
