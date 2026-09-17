<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('civil_applications', function (Blueprint $table) {
            $table->string('detail')->nullable()->after('claimed_partner_street');
        });
    }

    public function down(): void
    {
        Schema::table('civil_applications', function (Blueprint $table) {
            $table->dropColumn('detail');
        });
    }
};
