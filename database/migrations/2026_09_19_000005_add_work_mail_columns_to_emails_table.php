<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->foreignId('employment_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('folder', 10)->default('inbox');
            $table->string('recipient_name')->nullable();
            $table->string('recipient_address')->nullable();
            $table->string('action', 40)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropConstrainedForeignId('employment_id');
            $table->dropColumn(['folder', 'recipient_name', 'recipient_address', 'action']);
        });
    }
};
