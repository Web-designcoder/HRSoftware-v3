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
        // ───── CREATE TEST ADMIN USER ─────
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@hr-software.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'first_name' => 'Admin',
            'last_name' => 'User',
            'terms_accepted_at' => null,
        ]);

        // ───── CREATE TEST EMPLOYER USER ─────
        $testEmployerUser = User::factory()->create([
            'name' => 'Test Employer',
            'email' => 'employer@hr-software.com',
            'password' => bcrypt('password'),
            'role' => 'employer',
            'first_name' => 'Test',
            'last_name' => 'Employer',
            'terms_accepted_at' => null,
        ]);

        // ───── CREATE TEST CANDIDATE USER ─────
        $testCandidateUser = User::factory()->create([
            'name' => 'Test Candidate',
            'email' => 'candidate@hr-software.com',
            'password' => bcrypt('password'),
            'role' => 'candidate',
            'first_name' => 'Test',
            'last_name' => 'Candidate',
            'terms_accepted_at' => null,
        ]);

        // ───── CREATE OTHER EMPLOYER USERS ─────
        $employers = User::factory(19)->create([
            'role' => 'employer',
            'terms_accepted_at' => null,
        ]);

        // Create employer profiles for each employer user
        $employerProfiles = [];
        
        // Create profile for test employer
        $employerProfiles[] = Employer::factory()->create([
            'user_id' => $testEmployerUser->id,
            'company_name' => 'Test Company',
        ]);
        
        // Create profiles for other employers
        foreach ($employers as $employerUser) {
            $employerProfiles[] = Employer::factory()->create([
                'user_id' => $employerUser->id,
            ]);
        }

        // ───── CREATE OTHER CANDIDATE USERS ─────
        $candidates = User::factory(199)->create([
            'role' => 'candidate',
            'terms_accepted_at' => null,
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
        $allCandidates = collect([$testCandidateUser])->merge($candidates);
        
        foreach ($allCandidates as $candidate) {
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
        $this->command->info('💼 EMPLOYER:');
        $this->command->info('   Email: employer@hr-software.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('👤 CANDIDATE:');
        $this->command->info('   Email: candidate@hr-software.com');
        $this->command->info('   Password: password');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('Total Users: ' . User::count());
        $this->command->info('Total Jobs: ' . Job::count());
        $this->command->info('Total Applications: ' . JobApplication::count());
        $this->command->info('═══════════════════════════════════════════');
    }
}