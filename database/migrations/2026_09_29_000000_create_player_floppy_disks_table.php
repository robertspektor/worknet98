<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_type', 'product_id']);
            $table->index(['user_id', 'product_type', 'product_id']);
        });

        Schema::table('floppy_disks', function (Blueprint $table) {
            $table->dropColumn('program');
            $table->unsignedSmallInteger('pack_size')->default(1);
            $table->json('files')->nullable();
        });

        Schema::create('player_floppy_disks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floppy_disk_id')->constrained();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 20);
            $table->string('label', 24)->nullable();
            $table->boolean('is_write_protected');
            $table->timestamps();

            $table->index(['user_id', 'id']);
        });

        DB::statement("CREATE UNIQUE INDEX player_floppy_disks_one_starter_disk ON player_floppy_disks (user_id, floppy_disk_id) WHERE source = 'starter'");

        Schema::create('disk_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_floppy_disk_id')->constrained()->cascadeOnDelete();
            $table->string('name', 12);
            $table->string('kind', 20);
            $table->text('body')->nullable();
            $table->string('content_key')->nullable();
            $table->string('program')->nullable();
            $table->unsignedInteger('size_bytes');
            $table->timestamps();

            $table->unique(['player_floppy_disk_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disk_files');
        Schema::dropIfExists('player_floppy_disks');

        Schema::table('floppy_disks', function (Blueprint $table) {
            $table->dropColumn(['pack_size', 'files']);
            $table->string('program')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'product_type', 'product_id']);
            $table->unique(['user_id', 'product_type', 'product_id']);
        });
    }
};
