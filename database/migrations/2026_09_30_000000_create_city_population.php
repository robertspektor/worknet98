<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->createCities();
        $this->createHouseholds();
        $this->createPeople();

        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['branch_id', 'slug']);
            $table->dropColumn(['slug', 'name', 'street', 'city', 'phone', 'email_address']);
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->unique(['branch_id', 'person_id']);
        });

        Schema::table('technicians', function (Blueprint $table) {
            $table->dropUnique(['branch_id', 'slug']);
            $table->dropColumn(['slug', 'name']);
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->unique(['branch_id', 'person_id']);
        });

        Schema::table('positions', function (Blueprint $table) {
            $table->dropColumn('npc_name');
            $table->renameColumn('npc_address', 'work_address');
            $table->foreignId('person_id')->constrained('people')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('person_id');
            $table->renameColumn('work_address', 'npc_address');
            $table->string('npc_name')->default('');
        });

        Schema::table('technicians', function (Blueprint $table) {
            $table->dropUnique(['branch_id', 'person_id']);
            $table->dropConstrainedForeignId('person_id');
            $table->string('slug')->default('');
            $table->string('name')->default('');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropUnique(['branch_id', 'person_id']);
            $table->dropConstrainedForeignId('person_id');

            foreach (['slug', 'name', 'street', 'city', 'phone', 'email_address'] as $column) {
                $table->string($column)->default('');
            }
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('city_id');
        });

        Schema::dropIfExists('people');
        Schema::dropIfExists('households');
        Schema::dropIfExists('cities');
    }

    private function createCities(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('locale', 5);
            $table->timestamps();
        });
    }

    private function createHouseholds(): void
    {
        Schema::create('households', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->string('district');
            $table->string('street');
            $table->string('phone')->nullable();
            $table->timestamps();

            $table->unique(['city_id', 'district', 'street']);
        });
    }

    private function createPeople(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnDelete();
            $table->foreignId('household_id')->constrained()->cascadeOnDelete();
            $table->string('slug');
            $table->string('name');
            $table->string('email_address')->nullable();
            $table->timestamps();

            $table->unique(['city_id', 'slug']);
        });
    }
};
