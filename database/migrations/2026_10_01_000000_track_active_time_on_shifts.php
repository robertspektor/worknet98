<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropUnique(['employment_id', 'work_date']);
            $table->dropColumn('work_date');
            $table->timestamp('last_active_at')->nullable();
            $table->unsignedInteger('worked_seconds')->default(0);
            $table->boolean('clocked_out_automatically')->default(false);
            $table->index(['employment_id', 'clocked_out_at']);
        });

        DB::table('shifts')->update(['last_active_at' => DB::raw('coalesce(clocked_out_at, clocked_in_at)')]);

        Schema::table('shifts', function (Blueprint $table) {
            $table->timestamp('last_active_at')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('shifts', function (Blueprint $table) {
            $table->dropIndex(['employment_id', 'clocked_out_at']);
            $table->dropColumn(['last_active_at', 'worked_seconds', 'clocked_out_automatically']);
            $table->date('work_date')->nullable();
        });
    }
};
