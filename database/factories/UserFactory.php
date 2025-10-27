<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password = null;
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'salutation' => fake()->randomElement(['Mr', 'Ms', 'Mrs', 'Dr']),
            'first_name' => fake()->firstName(),
            'last_name'  => fake()->lastName(),
            'name'       => null, // auto-filled by model boot
            'email'      => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'   => static::$password ??= Hash::make('password'),
            'role'       => 'candidate',
            'phone'      => fake()->phoneNumber(),
            'address_line1' => fake()->streetAddress(),
            'address_line2' => fake()->optional()->secondaryAddress(),
            'city'       => fake()->randomElement(['Perth', 'Sydney', 'Melbourne', 'Brisbane', 'Adelaide']),
            'postcode'   => fake()->postcode(),
            'country'    => 'Australia',
            'remember_token' => Str::random(10),
        ];
    }

    /** Unverified email */
    public function unverified(): static
    {
        return $this->state(fn() => ['email_verified_at' => null]);
    }

    /** Admin */
    public function admin(): static
    {
        return $this->state(fn() => ['role' => 'admin']);
    }

    /** Consultant */
    public function consultant(): static
    {
        return $this->state(fn() => ['role' => 'consultant']);
    }

    /** Employer contact */
    public function employer(): static
    {
        return $this->state(fn() => ['role' => 'employer']);
    }

    /** Candidate */
    public function candidate(): static
    {
        return $this->state(fn() => ['role' => 'candidate']);
    }
}
