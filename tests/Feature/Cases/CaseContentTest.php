<?php

use App\Cases\CaseCatalog;
use App\Cases\Templates\CaseTemplateCatalog;
use App\Models\Branch;
use App\Models\Technician;
use App\Workplace\Availability;
use Database\Seeders\BranchSeeder;
use Database\Seeders\CitySeeder;
use Database\Seeders\CompanySeeder;
use Illuminate\Support\Facades\File;

beforeEach(fn () => $this->seed([CompanySeeder::class, CitySeeder::class, BranchSeeder::class]));

it('only references known customers, colleagues and technicians in case content', function () {
    foreach (Branch::with('company')->get() as $branch) {
        $definitions = app(CaseCatalog::class)->forCompany($branch->company);

        foreach ($definitions as $scripted) {
            $definition = $scripted->definition;
            expect($branch->customers()->ofPerson($definition->requestMail['customer'])->exists())->toBeTrue();

            foreach ($definition->messages as $message) {
                expect($branch->positions()->where('slug', $message->sender)->exists())->toBeTrue();
            }
        }

        $path = database_path("content/cases/{$branch->company->slug}.json");
        preg_match_all('/"technician": "([a-z-]+)"/', File::exists($path) ? File::get($path) : '', $technicians);

        foreach ($technicians[1] as $slug) {
            expect($branch->technicians()->ofPerson($slug)->exists())->toBeTrue();
        }
    }
});

it('can route and staff every case template in every branch of its company', function () {
    foreach (Branch::with('company')->get() as $branch) {
        foreach (app(CaseTemplateCatalog::class)->forCompany($branch->company) as $template) {
            $technicians = $branch->technicians()->get();

            expect($branch->positions()->responsibleFor($template->responsibility)->exists())->toBeTrue()
                ->and($technicians->contains(fn (Technician $technician): bool => $technician->hasSkill($template->skill)))->toBeTrue()
                ->and(array_keys($template->availabilityNotes))->toEqualCanonicalizing(array_column(Availability::cases(), 'value'))
                ->and(array_keys($template->outcomeFeedback))->toEqualCanonicalizing(['availability', 'skill', 'urgency']);
        }
    }
});
