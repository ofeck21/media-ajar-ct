<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('student_groups', 'score')) {
            Schema::table('student_groups', function (Blueprint $table) {
                $table->integer('score')->default(0)->after('name');
            });
        }
    }

    public function down(): void
    {
        Schema::table('student_groups', function (Blueprint $table) {
            $table->dropColumn('score');
        });
    }
};
