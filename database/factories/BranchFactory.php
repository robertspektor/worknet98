<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'company_id' => Company::factory(),
            'slug' => Str::slug($name),
            'name' => $name,
            'office_address' => 'office@'.Str::slug($name).'.wn',
        ];
    }
}
