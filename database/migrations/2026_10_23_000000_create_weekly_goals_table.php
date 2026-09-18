<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_id')->constrained()->cascadeOnDelete();
            $table->string('week', 10);
            $table->unsignedSmallInteger('target');
            $table->unsignedSmallInteger('resolved_cases');
            $table->unsignedInteger('bonus');
            $table->timestamp('achieved_at');
            $table->timestamps();
            $table->unique(['employment_id', 'week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_goals');
    }
};
