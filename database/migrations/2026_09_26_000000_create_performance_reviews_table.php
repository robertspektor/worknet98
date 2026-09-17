<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('performance_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_id')->constrained()->cascadeOnDelete();
            $table->string('period', 7);
            $table->json('metric_totals');
            $table->json('metric_changes');
            $table->integer('score');
            $table->string('rating', 20);
            $table->integer('bonus');
            $table->timestamps();

            $table->unique(['employment_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_reviews');
    }
};
