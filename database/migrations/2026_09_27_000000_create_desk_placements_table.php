<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('desk_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item', 40);
            $table->float('x');
            $table->float('y');
            $table->timestamps();

            $table->unique(['user_id', 'item']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desk_placements');
    }
};
