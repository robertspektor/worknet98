<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hardware_parts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('slot', 20);
            $table->unsignedInteger('speed_mhz');
            $table->boolean('is_starter')->default(false);
            $table->unsignedInteger('price')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hardware_parts');
    }
};
