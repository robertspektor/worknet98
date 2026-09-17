<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->foreignId('booked_by_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->timestamp('executed_at')->nullable()->index();
        });

        DB::table('appointments')->whereNotNull('booked_by_employment_id')->update([
            'booked_by_position_id' => DB::raw('(select position_id from employments where employments.id = appointments.booked_by_employment_id)'),
        ]);
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('executed_at');
            $table->dropConstrainedForeignId('booked_by_position_id');
        });
    }
};
