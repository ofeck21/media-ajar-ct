<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticQuestion extends Model
{
    protected $fillable = [
        'question',
        'options',
        'correct_answer_index',
    ];

    protected $casts = [
        'options' => 'array',
    ];
}
