<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobApplicationFactory extends Factory
{
    protected $model = JobApplication::class;

    public function definition(): array
    {
        return [
            'attention_to_detail' => fake()->optional(0.7)->sentence(20),
            'customer_management' => fake()->optional(0.7)->sentence(20),
            'market_understanding' => fake()->optional(0.7)->sentence(20),
            'sales_and_business_development' => fake()->optional(0.7)->sentence(20),
            'ambition' => fake()->optional(0.7)->sentence(20),
            'leadership_skills' => fake()->optional(0.7)->sentence(20),
            'risk_assessment' => fake()->optional(0.7)->sentence(20),
            'status' => 'pending',
        ];
    }
}