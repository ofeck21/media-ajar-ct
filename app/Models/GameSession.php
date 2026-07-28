<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GameSession extends Model
{
    protected $fillable = [
        'classroom_id',
        'teacher_id',
        'name',
        'duration_minutes',
        'status',
        'started_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
    ];

    public function classroom(): BelongsTo
    {
        return $this->belongsTo(Classroom::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function diagnosticResults(): HasMany
    {
        return $this->hasMany(DiagnosticResult::class);
    }

    public function studentGroups(): HasMany
    {
        return $this->hasMany(StudentGroup::class);
    }
}
