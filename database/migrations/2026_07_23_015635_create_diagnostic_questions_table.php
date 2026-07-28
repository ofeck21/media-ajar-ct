<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diagnostic_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->json('options'); // Store options as JSON array
            $table->integer('correct_answer_index'); // Index in the options array
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diagnostic_questions');
    }
};
