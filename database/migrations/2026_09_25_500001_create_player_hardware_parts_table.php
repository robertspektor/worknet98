<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_hardware_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('hardware_part_id')->constrained()->cascadeOnDelete();
            $table->timestamp('installed_at')->nullable();
            $table->boolean('needs_thermal_paste')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'hardware_part_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_hardware_parts');
    }
};
