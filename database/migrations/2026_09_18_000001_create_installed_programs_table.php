<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('installed_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floppy_disk_id')->constrained()->cascadeOnDelete();
            $table->string('program');
            $table->timestamp('installed_at');
            $table->timestamps();

            $table->unique(['user_id', 'program']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('installed_programs');
    }
};
