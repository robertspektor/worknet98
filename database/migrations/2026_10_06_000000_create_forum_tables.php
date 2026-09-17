<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_position_id')->constrained('positions')->cascadeOnDelete();
            $table->foreignId('author_employment_id')->nullable()->constrained('employments')->nullOnDelete();
            $table->string('slug')->nullable();
            $table->string('title');
            $table->timestamp('last_posted_at');
            $table->timestamps();

            $table->unique(['company_id', 'slug']);
        });

        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_thread_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_position_id')->constrained('positions')->cascadeOnDelete();
            $table->foreignId('author_employment_id')->nullable()->constrained('employments')->nullOnDelete();
            $table->text('body');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_posts');
        Schema::dropIfExists('forum_threads');
    }
};
