<?php

namespace Database\Factories;

use App\Models\Employer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployerFactory extends Factory
{
    protected $model = Employer::class;

    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'company_description' => fake()->paragraph(3),
            'website' => fake()->optional()->url(),
            'industry' => fake()->randomElement([
                'Technology',
                'Healthcare',
                'Finance',
                'Education',
                'Retail',
                'Manufacturing',
                'Consulting',
                'Real Estate',
                'Marketing',
                'Construction'
            ]),
        ];
    }
}