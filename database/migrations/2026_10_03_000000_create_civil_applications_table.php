<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('civil_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 20);
            $table->foreignId('applicant_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('people')->cascadeOnDelete();
            $table->string('claimed_district');
            $table->string('claimed_street');
            $table->string('claimed_partner_district')->nullable();
            $table->string('claimed_partner_street')->nullable();
            $table->string('new_district')->nullable();
            $table->string('new_street')->nullable();
            $table->date('moved_on');
            $table->string('decision', 20)->nullable();
            $table->boolean('matched_registry')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('decided_by_employment_id')->nullable()->constrained('employments')->nullOnDelete();
            $table->foreignId('decided_by_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('work_cases', function (Blueprint $table) {
            $table->foreignId('civil_application_id')->nullable()->unique()->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('civil_application_id');
        });

        Schema::dropIfExists('civil_applications');
    }
};
