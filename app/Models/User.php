<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'username',
        'nisn',
        'password',
        'role',
        'school_id',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function initials(): string
    {
        $initials = Str::initials($this->name, true);
        return Str::length($initials) > 1
            ? Str::substr($initials, 0, 1).Str::substr($initials, -1)
            : $initials;
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    // Guru: kelas yang diajar (teacher_id)
    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    // Siswa: kelas yang diikuti (pivot classroom_student)
    public function studentClassrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_student', 'student_id', 'classroom_id');
    }

    // Shortcut: ambil kelas pertama siswa
    public function getClassroom()
    {
        return $this->studentClassrooms()->first();
    }

    public function groups()
    {
        return $this->belongsToMany(StudentGroup::class, 'group_student', 'student_id', 'student_group_id');
    }

    public function results()
    {
        return $this->hasMany(StudentResult::class, 'student_id');
    }

    public function diagnosticResults()
    {
        return $this->hasMany(DiagnosticResult::class, 'student_id');
    }

    /**
     * Get real-time login status of the student.
     */
    public function getLoginStatusAttribute(): array
    {
        try {
            $latestSession = \Illuminate\Support\Facades\DB::table('sessions')
                ->where('user_id', $this->id)
                ->orderBy('last_activity', 'desc')
                ->first();

            if ($latestSession) {
                // Online if activity within last 2 minutes (120 seconds)
                $isOnline = $latestSession->last_activity >= (now()->subSeconds(120)->timestamp);
                if ($isOnline) {
                    return [
                        'label' => 'Online',
                        'color' => 'emerald',
                        'is_online' => true,
                    ];
                }

                $lastActiveDate = \Illuminate\Support\Carbon::createFromTimestamp($latestSession->last_activity);
                return [
                    'label' => 'Pernah Login (' . $lastActiveDate->diffForHumans() . ')',
                    'color' => 'amber',
                    'is_online' => false,
                ];
            }

            if ($this->last_login_at) {
                return [
                    'label' => 'Pernah Login (' . $this->last_login_at->diffForHumans() . ')',
                    'color' => 'amber',
                    'is_online' => false,
                ];
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return [
            'label' => 'Offline',
            'color' => 'zinc',
            'is_online' => false,
        ];
    }
}
