<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floppy_disk_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floppy_disk_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('price');
            $table->timestamp('delivers_at');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('unpacked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'floppy_disk_id']);
            $table->index(['delivered_at', 'delivers_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floppy_disk_orders');
    }
};
