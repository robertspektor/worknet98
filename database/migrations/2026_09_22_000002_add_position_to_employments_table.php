<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            $table->foreignId('position_id')->unique()->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('position_id');
        });
    }
};
