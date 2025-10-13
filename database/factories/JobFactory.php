<?php

namespace Database\Factories;

use App\Models\Employer;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        $jobTitles = [
            'Senior Software Engineer',
            'Marketing Manager',
            'Sales Representative',
            'HR Coordinator',
            'Financial Analyst',
            'Product Manager',
            'Data Scientist',
            'Customer Success Manager',
            'Operations Manager',
            'Business Development Manager',
            'UX/UI Designer',
            'Account Executive',
            'Project Manager',
            'Content Writer',
            'IT Support Specialist'
        ];

        return [
            'title' => fake()->randomElement($jobTitles),
            'description' => fake()->paragraphs(3, true),
            'assignment_overview' => fake()->paragraph(2),
            'location' => fake()->city(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'salary' => fake()->numberBetween(40000, 150000),
            'experience' => fake()->randomElement(Job::$experience),
            'category' => fake()->randomElement(Job::$category),
            'date_posted' => fake()->dateTimeBetween('-90 days', 'now'),
            'managed_by' => fake()->name(),
        ];
    }
}