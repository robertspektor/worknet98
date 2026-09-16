<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('work_case_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('message_slug')->nullable();
            $table->string('contact_name');
            $table->boolean('is_from_player');
            $table->text('body');
            $table->json('replies')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamp('sent_at');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['employment_id', 'sent_at']);
            $table->unique(['work_case_id', 'message_slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
