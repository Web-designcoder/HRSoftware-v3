<?php

namespace Database\Factories;

use App\Models\Employer;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployerFactory extends Factory
{
    protected $model = Employer::class;

    public function definition(): array
    {
        $cities = [
            ['city' => 'Perth', 'postcode' => '6000'],
            ['city' => 'Sydney', 'postcode' => '2000'],
            ['city' => 'Melbourne', 'postcode' => '3000'],
            ['city' => 'Brisbane', 'postcode' => '4000'],
            ['city' => 'Adelaide', 'postcode' => '5000'],
        ];
        $c = $cities[array_rand($cities)];

        return [
            // Updated to new schema (no company_name)
            'name'          => fake()->company() . ' Pty Ltd',
            'phone'         => fake()->phoneNumber(),
            'email'         => fake()->companyEmail(),
            'address_line1' => fake()->streetAddress(),
            'address_line2' => null,
            'city'          => $c['city'],
            'postcode'      => $c['postcode'],
            'country'       => 'Australia',
            'industry'      => fake()->randomElement(['IT','Finance','Healthcare','Construction','Education','Marketing']),
        ];
    }
}
