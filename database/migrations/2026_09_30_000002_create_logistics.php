<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('vehicle', 20);
            $table->json('busy_tours');
            $table->timestamps();

            $table->unique(['branch_id', 'person_id']);
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('recipient_branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('repair_case_id')->unique()->constrained('work_cases')->cascadeOnDelete();
            $table->string('contents');
            $table->string('size', 20);
            $table->date('due_date');
            $table->string('due_slot', 5);
            $table->foreignId('driver_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tour_date')->nullable();
            $table->string('tour', 20)->nullable();
            $table->foreignId('planned_by_employment_id')->nullable()->constrained('employments')->nullOnDelete();
            $table->foreignId('planned_by_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::table('work_cases', function (Blueprint $table) {
            $table->foreignId('shipment_id')->nullable()->unique()->constrained()->nullOnDelete();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->string('contact_address')->nullable();
        });

        Schema::table('appointments', function (Blueprint $table) {
            $table->timestamp('failed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropColumn('failed_at');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('contact_address');
        });

        Schema::table('work_cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('shipment_id');
        });

        Schema::dropIfExists('shipments');
        Schema::dropIfExists('drivers');
    }
};
