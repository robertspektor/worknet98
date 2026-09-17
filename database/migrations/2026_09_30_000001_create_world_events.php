<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('world_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('world_events')->nullOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('key')->unique();
            $table->string('type', 50);
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index(['city_id', 'type']);
        });

        Schema::table('work_cases', function (Blueprint $table) {
            $table->dropUnique(['demand_key']);
            $table->dropColumn('demand_key');
            $table->foreignId('world_event_id')->nullable()->unique()->constrained()->nullOnDelete();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->json('services')->default('[]');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('services');
        });

        Schema::table('work_cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('world_event_id');
            $table->string('demand_key')->nullable()->unique();
        });

        Schema::dropIfExists('world_events');
    }
};
