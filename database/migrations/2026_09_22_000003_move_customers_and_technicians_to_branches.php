<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['customers', 'technicians'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique(['company_id', 'slug']);
                $table->dropConstrainedForeignId('company_id');
                $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
                $table->unique(['branch_id', 'slug']);
            });
        }
    }

    public function down(): void
    {
        foreach (['customers', 'technicians'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropUnique(['branch_id', 'slug']);
                $table->dropConstrainedForeignId('branch_id');
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->unique(['company_id', 'slug']);
            });
        }
    }
};
