<?php

namespace Database\Seeders;

use App\Models\CandidateProfile;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // 1) CORE TEST ACCOUNTS (EXACT EMAILS YOU WANT)
        // ─────────────────────────────────────────────────────────────────────
        $admin = User::factory()->admin()->create([
            'email'      => 'admin@hr-software.com',
            'first_name' => 'Admin',
            'last_name'  => 'User',
        ]);

        $consultant = User::factory()->consultant()->create([
            'email'      => 'consultant@hr-software.com',
            'first_name' => 'Consultant',
            'last_name'  => 'User',
        ]);

        $employerUser = User::factory()->employer()->create([
            'email'      => 'employer@hr-software.com',
            'first_name' => 'Test',
            'last_name'  => 'Employer',
        ]);

        $candidateUser = User::factory()->candidate()->create([
            'email'      => 'candidate@hr-software.com',
            'first_name' => 'Test',
            'last_name'  => 'Candidate',
        ]);

        CandidateProfile::factory()->create([
            'user_id' => $candidateUser->id,
        ]);

        // ─────────────────────────────────────────────────────────────────────
        // 2) COMPANIES (EMPLOYERS) & EMPLOYER CONTACTS
        // ─────────────────────────────────────────────────────────────────────
        // Create the test employer company and link the employer user via pivot
        $testEmployerCompany = Employer::factory()->create([
            'name'     => 'Test Employer Company',
            'email'    => 'info@testemployer.com',
            'city'     => 'Perth',
            'country'  => 'Australia',
            'industry' => 'Professional Services',
        ]);

        $employerUser->employers()->attach($testEmployerCompany->id, [
            'position'          => 'HR Manager',
            'permission_level'  => 'level3',
        ]);

        // Create a few additional companies & contacts
        $extraEmployers  = Employer::factory()->count(4)->create(); // total ~5 companies incl. test
        $employerContacts = User::factory()->count(8)->employer()->create();

        foreach ($employerContacts as $contact) {
            $company = $extraEmployers->random();
            $contact->employers()->attach($company->id, [
                'position'         => fake()->randomElement(['HR Manager','Recruiter','Owner']),
                'permission_level' => fake()->randomElement(['level1','level2','level3']),
            ]);
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3) CONSULTANT POOL (ADMIN CAN ACT AS CONSULTANT)
        // ─────────────────────────────────────────────────────────────────────
        $consultantPool = collect([$consultant, $admin]);

        // Optional: add a couple more consultants
        $moreConsultants = User::factory()->count(2)->consultant()->create();
        $consultantPool  = $consultantPool->merge($moreConsultants);

        // ─────────────────────────────────────────────────────────────────────
        // 4) CANDIDATES (+ PROFILES)
        // ─────────────────────────────────────────────────────────────────────
        $extraCandidates = User::factory()->count(18)->candidate()->create();
        foreach ($extraCandidates as $c) {
            CandidateProfile::factory()->create(['user_id' => $c->id]);
        }

        // Pool of all candidates (ensure the test candidate is included)
        $allCandidates = $extraCandidates->push($candidateUser);

        // ─────────────────────────────────────────────────────────────────────
        // 5) JOBS
        // - Create some jobs for the test company explicitly (so employer sees them)
        // - Create additional jobs across other companies
        // - Assign consultant_id from the consultant pool (admin included)
        // ─────────────────────────────────────────────────────────────────────
        $jobs = collect();

        // Ensure at least 3 jobs for the Test Employer Company
        $testCompanyJobs = Job::factory()->count(3)->make()->each(function ($job) use ($testEmployerCompany, $consultantPool) {
            $job->employer_id   = $testEmployerCompany->id;
            $job->consultant_id = $consultantPool->random()->id;
            $job->save();
        });
        $jobs = $jobs->merge($testCompanyJobs);

        // Create more jobs for other companies
        $otherCompanies = $extraEmployers;
        $moreJobs = Job::factory()->count(12)->make()->each(function ($job) use ($otherCompanies, $consultantPool) {
            $job->employer_id   = $otherCompanies->random()->id;
            $job->consultant_id = $consultantPool->random()->id;
            $job->save();
        });
        $jobs = $jobs->merge($moreJobs);

        // ─────────────────────────────────────────────────────────────────────
        // 6) JOB VISIBILITY (job_user) + JOB APPLICATIONS
        // - Assign 3–5 candidates visibility per job
        // - For 2–4 of those, create actual applications
        // - Ensure the test candidate applies to at least 1 job owned by Test Employer
        // ─────────────────────────────────────────────────────────────────────
        foreach ($jobs as $job) {
            $visibleCandidates = $allCandidates->random(rand(3, 5));
            $job->assignedCandidates()->syncWithoutDetaching($visibleCandidates->pluck('id')->toArray());

            $applicants = $visibleCandidates->random(min(rand(2, 4), $visibleCandidates->count()));
            foreach ($applicants as $candidate) {
                JobApplication::factory()->create([
                    'job_id'     => $job->id,
                    'user_id'    => $candidate->id,
                    'first_name' => $candidate->first_name,
                    'last_name'  => $candidate->last_name,
                    'city'       => $candidate->city,
                    'postcode'   => $candidate->postcode,
                    'status'     => fake()->randomElement(['pending','reviewing','accepted','rejected']),
                ]);
            }
        }

        // Guarantee: test candidate applies to at least one job under Test Employer Company
        $oneTestJob = $testCompanyJobs->random();
        if (!JobApplication::where('job_id', $oneTestJob->id)->where('user_id', $candidateUser->id)->exists()) {
            // ensure the candidate has visibility
            $oneTestJob->assignedCandidates()->syncWithoutDetaching([$candidateUser->id]);

            JobApplication::factory()->create([
                'job_id'     => $oneTestJob->id,
                'user_id'    => $candidateUser->id,
                'first_name' => $candidateUser->first_name,
                'last_name'  => $candidateUser->last_name,
                'city'       => $candidateUser->city,
                'postcode'   => $candidateUser->postcode,
                'status'     => 'pending',
            ]);
        }

        // ─────────────────────────────────────────────────────────────────────
        // 7) OUTPUT CREDENTIALS (EXACT TEXT YOU WANT)
        // ─────────────────────────────────────────────────────────────────────
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('  TEST USER CREDENTIALS');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('');
        $this->command->info('🔑 ADMIN:     admin@hr-software.com / password');
        $this->command->info('🔑 CONSULTANT: consultant@hr-software.com / password');
        $this->command->info('💼 EMPLOYER:  employer@hr-software.com / password');
        $this->command->info('👤 CANDIDATE: candidate@hr-software.com / password');
        $this->command->info('');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('Users: ' . User::count());
        $this->command->info('Employers: ' . Employer::count());
        $this->command->info('Jobs: ' . Job::count());
        $this->command->info('Applications: ' . JobApplication::count());
        $this->command->info('═══════════════════════════════════════════');
    }
}
