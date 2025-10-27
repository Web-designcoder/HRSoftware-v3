<?php

namespace Database\Factories;

use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $titles = [
            'Sales Manager', 'Project Coordinator', 'Software Developer',
            'Marketing Specialist', 'Account Manager', 'Business Analyst',
            'Customer Success Officer', 'Operations Supervisor'
        ];

        return [
            'employer_id'  => Employer::factory(),
            'title'        => fake()->randomElement($titles),
            'description'  => fake()->paragraphs(3, true),
            'assignment_overview' => fake()->paragraph(),
            'location'     => 'Australia',
            'city'         => fake()->randomElement(['Perth','Sydney','Melbourne','Brisbane','Adelaide']),
            'country'      => 'Australia',
            'salary'       => fake()->numberBetween(60000, 140000),
            'experience'   => fake()->randomElement(Job::$experience),
            'category'     => fake()->randomElement(Job::$category),
            'date_posted'  => now(),
            'managed_by'   => 'HR Department',
            'consultant_id'=> User::factory()->consultant(),
        ];
    }
}
