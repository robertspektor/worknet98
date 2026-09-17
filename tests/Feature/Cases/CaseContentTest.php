<?php

use App\Cases\CaseCatalog;
use App\Models\Branch;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CompanySeeder;
use Illuminate\Support\Facades\File;

beforeEach(fn () => $this->seed([CompanySeeder::class, BranchSeeder::class]));

it('only references known customers, colleagues and technicians in case content', function () {
    foreach (Branch::with('company')->get() as $branch) {
        $definitions = app(CaseCatalog::class)->forCompany($branch->company);

        foreach ($definitions as $definition) {
            expect($branch->customers()->where('slug', $definition->requestMail['customer'])->exists())->toBeTrue();

            foreach ($definition->messages as $message) {
                expect($branch->positions()->where('slug', $message->sender)->exists())->toBeTrue();
            }
        }

        $path = database_path("content/cases/{$branch->company->slug}.json");
        preg_match_all('/"technician": "([a-z-]+)"/', File::exists($path) ? File::get($path) : '', $technicians);

        foreach ($technicians[1] as $slug) {
            expect($branch->technicians()->where('slug', $slug)->exists())->toBeTrue();
        }
    }
});
