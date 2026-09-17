<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COPIED_COLUMNS = 'user_id, price, delivers_at, delivered_at, unpacked_at, created_at, updated_at';

    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('product_type', 30);
            $table->unsignedBigInteger('product_id');
            $table->unsignedInteger('price');
            $table->timestamp('delivers_at');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('unpacked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_type', 'product_id']);
            $table->index(['delivered_at', 'delivers_at']);
            $table->index(['product_type', 'product_id']);
        });

        DB::statement('insert into orders (product_type, product_id, '.self::COPIED_COLUMNS.") select 'floppy_disk', floppy_disk_id, ".self::COPIED_COLUMNS.' from floppy_disk_orders order by id');

        Schema::drop('floppy_disk_orders');
    }

    public function down(): void
    {
        Schema::create('floppy_disk_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('floppy_disk_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('price');
            $table->timestamp('delivers_at');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('unpacked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'floppy_disk_id']);
            $table->index(['delivered_at', 'delivers_at']);
        });

        DB::statement('insert into floppy_disk_orders (floppy_disk_id, '.self::COPIED_COLUMNS.') select product_id, '.self::COPIED_COLUMNS." from orders where product_type = 'floppy_disk' order by id");

        Schema::drop('orders');
    }
};
