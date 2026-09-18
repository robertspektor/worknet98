<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_cases', function (Blueprint $table) {
            $table->foreignId('taken_over_from_employment_id')->nullable()->after('employment_id')->constrained('employments')->nullOnDelete();
            $table->timestamp('taken_over_at')->nullable()->after('seen_at');
        });
    }

    public function down(): void
    {
        Schema::table('work_cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('taken_over_from_employment_id');
            $table->dropColumn('taken_over_at');
        });
    }
};
