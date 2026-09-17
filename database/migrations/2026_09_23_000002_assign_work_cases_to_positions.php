<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_cases', function (Blueprint $table) {
            $table->dropUnique(['employment_id', 'case_slug']);
            $table->foreignId('branch_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained()->cascadeOnDelete();
            $table->timestamp('npc_due_at')->nullable();
            $table->foreignId('employment_id')->nullable()->change();
        });

        DB::table('work_cases')->update([
            'position_id' => DB::raw('(select position_id from employments where employments.id = work_cases.employment_id)'),
        ]);
        DB::table('work_cases')->update([
            'branch_id' => DB::raw('(select branch_id from positions where positions.id = work_cases.position_id)'),
        ]);

        Schema::table('work_cases', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable(false)->change();
            $table->foreignId('position_id')->nullable(false)->change();
            $table->index(['position_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('work_cases', function (Blueprint $table) {
            $table->dropIndex(['position_id', 'status']);
            $table->dropConstrainedForeignId('customer_id');
            $table->dropConstrainedForeignId('position_id');
            $table->dropConstrainedForeignId('branch_id');
            $table->dropColumn('npc_due_at');
            $table->unique(['employment_id', 'case_slug']);
        });
    }
};
