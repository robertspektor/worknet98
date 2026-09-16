<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('locale', 5)->index();
            $table->string('name');
            $table->string('industry');
            $table->string('tagline');
            $table->text('description');
            $table->string('hr_contact_name');
            $table->string('hr_contact_address');
            $table->text('hiring_note');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
