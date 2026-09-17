<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique(['employment_id', 'technician_id', 'date', 'slot']);
            $table->dropConstrainedForeignId('employment_id');
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('booked_by_employment_id')->nullable()->constrained('employments')->nullOnDelete();
            $table->unique(['technician_id', 'date', 'slot']);
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique(['technician_id', 'date', 'slot']);
            $table->dropConstrainedForeignId('booked_by_employment_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->foreignId('employment_id')->constrained()->cascadeOnDelete();
            $table->unique(['employment_id', 'technician_id', 'date', 'slot']);
        });
    }
};
