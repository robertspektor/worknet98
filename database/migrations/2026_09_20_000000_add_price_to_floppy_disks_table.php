<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('floppy_disks', function (Blueprint $table) {
            $table->unsignedInteger('price')->nullable()->after('is_starter');
        });
    }

    public function down(): void
    {
        Schema::table('floppy_disks', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
