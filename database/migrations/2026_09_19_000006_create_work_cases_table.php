<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_id')->constrained()->cascadeOnDelete();
            $table->string('case_slug');
            $table->string('status', 20);
            $table->timestamp('opened_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->unique(['employment_id', 'case_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_cases');
    }
};
