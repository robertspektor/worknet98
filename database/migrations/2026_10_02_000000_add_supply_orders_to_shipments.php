<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->foreignId('repair_case_id')->nullable()->change();
            $table->string('order_key')->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table) {
            $table->dropUnique(['order_key']);
            $table->dropColumn('order_key');
            $table->foreignId('repair_case_id')->nullable(false)->change();
        });
    }
};
