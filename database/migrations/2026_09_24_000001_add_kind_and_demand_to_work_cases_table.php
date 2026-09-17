<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_cases', function (Blueprint $table) {
            $table->string('kind', 20)->default('template');
            $table->string('demand_key')->nullable()->unique();
        });

        DB::table('work_cases')->whereNull('customer_id')->update(['kind' => 'scripted']);
        DB::table('work_cases')->whereNull('customer_id')->get()->each($this->assignScriptedCustomer(...));

        Schema::table('work_cases', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('work_cases', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->change();
            $table->dropUnique(['demand_key']);
            $table->dropColumn(['kind', 'demand_key']);
        });
    }

    private function assignScriptedCustomer(stdClass $workCase): void
    {
        $companySlug = DB::table('branches')->join('companies', 'companies.id', '=', 'branches.company_id')
            ->where('branches.id', $workCase->branch_id)
            ->value('companies.slug');
        $path = database_path("content/cases/{$companySlug}.json");
        $definition = collect(File::exists($path) ? File::json($path) : [])->firstWhere('slug', $workCase->case_slug);
        $customerId = DB::table('customers')
            ->where('branch_id', $workCase->branch_id)
            ->where('slug', $definition['request_mail']['customer'] ?? null)
            ->value('id');

        $customerId === null
            ? DB::table('work_cases')->where('id', $workCase->id)->delete()
            : DB::table('work_cases')->where('id', $workCase->id)->update(['customer_id' => $customerId]);
    }
};
