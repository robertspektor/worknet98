<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floppy_disks', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('kind', 20);
            $table->string('color', 20);
            $table->string('program')->nullable();
            $table->boolean('is_starter')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floppy_disks');
    }
};
