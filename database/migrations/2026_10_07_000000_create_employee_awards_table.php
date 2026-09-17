<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employment_id')->constrained()->cascadeOnDelete();
            $table->string('period', 7);
            $table->integer('score');
            $table->timestamps();

            $table->unique(['branch_id', 'period']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_awards');
    }
};
