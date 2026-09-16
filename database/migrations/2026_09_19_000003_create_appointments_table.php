<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('slot', 5);
            $table->timestamps();

            $table->unique(['employment_id', 'technician_id', 'date', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
