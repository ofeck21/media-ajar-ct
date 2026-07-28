<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StudentGroup extends Model
{
    protected $fillable = [
        'game_session_id',
        'name',
        'score',
    ];

    public function gameSession(): BelongsTo
    {
        return $this->belongsTo(GameSession::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_student', 'student_group_id', 'student_id')->withTimestamps();
    }
}
