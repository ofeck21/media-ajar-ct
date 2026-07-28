<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('student_results')) {
            Schema::create('student_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('game_session_id')->constrained()->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('student_group_id')->nullable()->constrained('student_groups')->nullOnDelete();
                $table->integer('diagnostic_score')->nullable();
                $table->string('diagnostic_level')->nullable();
                $table->integer('total_stars')->default(0);
                $table->integer('total_steps')->default(0);
                $table->json('reflections')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_results');
    }
};
