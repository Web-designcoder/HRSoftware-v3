<?php

namespace Database\Seeders;

use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ───── CREATE ADMIN USER ─────
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@hr-software.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'terms_accepted_at' => now(),
        ]);

        // ───── CREATE EMPLOYER USERS ─────
        $employers = User::factory(20)->create([
            'role' => 'employer',
            'terms_accepted_at' => now(),
        ]);

        // Create employer profiles for each employer user
        $employerProfiles = [];
        foreach ($employers as $employerUser) {
            $employerProfiles[] = Employer::factory()->create([
                'user_id' => $employerUser->id,
            ]);
        }

        // ───── CREATE CANDIDATE USERS ─────
        $candidates = User::factory(200)->create([
            'role' => 'candidate',
            'terms_accepted_at' => now(),
        ]);

        // ───── CREATE JOBS (by employers) ─────
        foreach ($employerProfiles as $employer) {
            Job::factory(rand(2, 8))->create([
                'employer_id' => $employer->id,
                'date_posted' => now()->subDays(rand(1, 90)),
            ]);
        }

        // ───── CREATE JOB APPLICATIONS (by candidates) ─────
        $allJobs = Job::all();
        
        foreach ($candidates as $candidate) {
            // Each candidate applies to 0-5 random jobs
            $jobsToApply = $allJobs->random(rand(0, 5));
            
            foreach ($jobsToApply as $job) {
                JobApplication::factory()->create([
                    'job_id' => $job->id,
                    'user_id' => $candidate->id,
                    'first_name' => $candidate->first_name,
                    'last_name' => $candidate->last_name,
                    'city' => $candidate->city,
                    'postcode' => $candidate->postcode,
                    'status' => fake()->randomElement(['pending', 'reviewing', 'accepted', 'rejected']),
                ]);
            }
        }

        // ───── DISPLAY LOGIN CREDENTIALS ─────
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  TEST USER CREDENTIALS');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🔑 ADMIN:');
        $this->command->info('   Email: admin@hr-software.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('💼 EMPLOYER (example):');
        $this->command->info('   Email: ' . $employers->first()->email);
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('👤 CANDIDATE (example):');
        $this->command->info('   Email: ' . $candidates->first()->email);
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('Total Users: ' . User::count());
        $this->command->info('Total Jobs: ' . Job::count());
        $this->command->info('Total Applications: ' . JobApplication::count());
        $this->command->info('═══════════════════════════════════════════');
    }
}