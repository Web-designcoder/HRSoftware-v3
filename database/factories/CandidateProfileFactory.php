<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CandidateProfileFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id'          => User::factory()->candidate(),
            'cv'               => null,
            'medical_check'    => null,
            'police_clearance' => null,
            'qualifications'   => ['Bachelor of Commerce'],
            'other_files'      => [],
        ];
    }
}
