<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\City;
use App\Models\Company;
use App\Models\Person;
use App\Models\Position;
use App\Organization\BranchCatalog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class BranchSeeder extends Seeder
{
    public function run(BranchCatalog $catalog): void
    {
        foreach ($catalog->companySlugs() as $slug) {
            $company = Company::query()->where('slug', $slug)->firstOrFail();

            foreach ($catalog->branchesOf($company) as $content) {
                $this->seedBranch($company, $content);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $content
     */
    private function seedBranch(Company $company, array $content): void
    {
        $branch = $company->branches()->updateOrCreate(['slug' => $content['slug']], [
            ...Arr::only($content, ['name', 'office_address']),
            'city_id' => City::query()->where('slug', $content['city'])->firstOrFail()->id,
        ]);

        $this->seedPositions($branch, $content);
        $this->seedRecords($branch, 'technicians', $content);
        $this->seedRecords($branch, 'customers', $content);
        $this->seedRecords($branch, 'drivers', $content);
    }

    /**
     * @param  array<string, mixed>  $content
     */
    private function seedPositions(Branch $branch, array $content): void
    {
        /** @var list<array<string, mixed>> $positions */
        $positions = $content['positions'];

        foreach ($positions as $position) {
            $branch->positions()->updateOrCreate(['slug' => $position['slug']], [
                ...Arr::only($position, ['title', 'work_address']),
                'person_id' => $this->personId($branch, $position['person']),
                'responsibilities' => $position['responsibilities'] ?? [],
                'job_opening_id' => $this->jobOpeningId($branch, $position['job_opening'] ?? null),
                'daily_salary' => $position['daily_salary'] ?? null,
                'promotion_excellent_reviews' => $position['promotion']['excellent_reviews'] ?? null,
                'promotion_clean_months' => $position['promotion']['clean_months'] ?? null,
            ]);
        }

        foreach ($positions as $position) {
            $record = Position::query()->whereBelongsTo($branch)->where('slug', $position['slug'])->sole();
            $record->update(['reports_to_position_id' => $this->positionId($branch, $position['reports_to'] ?? null)]);
            $record->promotionTargets()->sync($this->promotionTargetIds($branch, $position['promotion']['to'] ?? []));
        }
    }

    /**
     * @param  list<string>  $slugs
     * @return list<int>
     */
    private function promotionTargetIds(Branch $branch, array $slugs): array
    {
        return array_map(fn (string $slug): int => (int) $this->positionId($branch, $slug), $slugs);
    }

    private function personId(Branch $branch, string $slug): int
    {
        return Person::query()->where('city_id', $branch->city_id)->where('slug', $slug)->firstOrFail()->id;
    }

    private function jobOpeningId(Branch $branch, ?string $slug): ?int
    {
        return $slug === null ? null : $branch->company->jobOpenings()->where('slug', $slug)->firstOrFail()->id;
    }

    private function positionId(Branch $branch, ?string $slug): ?int
    {
        return $slug === null ? null : Position::query()->whereBelongsTo($branch)->where('slug', $slug)->firstOrFail()->id;
    }

    /**
     * @param  'technicians'|'customers'|'drivers'  $relation
     * @param  array<string, mixed>  $content
     */
    private function seedRecords(Branch $branch, string $relation, array $content): void
    {
        /** @var list<array<string, mixed>> $records */
        $records = $content[$relation] ?? [];

        foreach ($records as $record) {
            $branch->{$relation}()->updateOrCreate(
                ['person_id' => $this->personId($branch, $record['person'])],
                Arr::except($record, ['person']),
            );
        }
    }
}
