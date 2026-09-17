<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->unsignedInteger('daily_salary')->nullable();
            $table->unsignedSmallInteger('promotion_excellent_reviews')->nullable();
            $table->unsignedSmallInteger('promotion_clean_months')->nullable();
        });

        Schema::create('position_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->foreignId('target_position_id')->constrained('positions')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['position_id', 'target_position_id']);
        });

        Schema::table('employments', function (Blueprint $table) {
            $table->timestamp('position_started_at')->nullable();
        });
        DB::table('employments')->update(['position_started_at' => DB::raw('hired_at')]);
        Schema::table('employments', function (Blueprint $table) {
            $table->timestamp('position_started_at')->nullable(false)->change();
        });

        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->constrained();
        });
        DB::statement('update performance_reviews set position_id = employments.position_id from employments where employments.id = performance_reviews.employment_id');
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable(false)->change();
        });

        Schema::create('promotion_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performance_review_id')->constrained()->cascadeOnDelete();
            $table->foreignId('email_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 20);
            $table->foreignId('accepted_position_id')->nullable()->constrained('positions')->nullOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('promotion_offer_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('position_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('daily_salary');
            $table->timestamps();

            $table->unique(['promotion_offer_id', 'position_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_offer_options');
        Schema::dropIfExists('promotion_offers');

        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->dropConstrainedForeignId('position_id');
        });

        Schema::table('employments', function (Blueprint $table) {
            $table->dropColumn('position_started_at');
        });

        Schema::dropIfExists('position_promotions');

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn(['daily_salary', 'promotion_excellent_reviews', 'promotion_clean_months']);
        });
    }
};
