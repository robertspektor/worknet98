<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('job_opening_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reports_to_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->string('slug');
            $table->string('title');
            $table->string('npc_name');
            $table->string('npc_address');
            $table->timestamps();

            $table->unique(['branch_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('positions');
    }
};
