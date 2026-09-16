<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_id')->constrained()->cascadeOnDelete();
            $table->string('metric', 40);
            $table->integer('value')->default(0);
            $table->timestamps();

            $table->unique(['employment_id', 'metric']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_metrics');
    }
};
