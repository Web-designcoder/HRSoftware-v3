<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployerController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MyJobController;
use App\Http\Controllers\MyJobApplicationController;
use App\Http\Controllers\TermsController;
use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

/* ───────────────────────────────────────────────────
   GUEST ROUTES (No authentication required)
─────────────────────────────────────────────────── */

Route::get('login', fn() => to_route('auth.create'))->name('login');
Route::resource('auth', AuthController::class)->only(['create', 'store']);

// Public job listings (anyone can browse)
Route::get('/', [JobController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

/* ───────────────────────────────────────────────────
   AUTHENTICATED ROUTES
─────────────────────────────────────────────────── */

Route::middleware('auth')->group(function () {
    
    // Logout
    Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');
    
    // Terms & Conditions (must accept before accessing app)
    Route::get('/terms', [TermsController::class, 'show'])->name('terms.show');
    Route::post('/terms', [TermsController::class, 'accept'])->name('terms.accept');

    // Routes that require terms acceptance
    Route::middleware('terms.accepted')->group(function () {
        
        // Dashboard (role-based)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Account settings (all roles)
        Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('/account', [AccountController::class, 'update'])->name('account.update');
        Route::post('/account/upload-attachment', [AccountController::class, 'uploadAttachment'])
            ->name('account.upload');
        Route::delete('/account/delete-attachment', [AccountController::class, 'deleteAttachment'])
            ->name('account.delete');

        /* ─────────────────────────────────────────
           CANDIDATE ROUTES
        ───────────────────────────────────────── */
        Route::middleware('role:candidate')->group(function () {
            
            // Apply to jobs
            Route::get('/jobs/{job}/apply', [JobApplicationController::class, 'create'])
                ->name('job.application.create');
            Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])
                ->name('job.application.store');

            // View application
            Route::get('/jobs/{job}/application/{jobApplication}', [JobApplicationController::class, 'show'])
                ->name('job.application.show');

            // Withdraw application
            Route::delete('/jobs/{job}/application/{jobApplication}', [JobApplicationController::class, 'destroy'])
                ->name('job.application.destroy');

            // My applications list
            Route::get('/my-applications', [MyJobApplicationController::class, 'index'])
                ->name('my-job-applications.index');
            Route::delete('/my-applications/{myJobApplication}', [MyJobApplicationController::class, 'destroy'])
                ->name('my-job-applications.destroy');
        });

        /* ─────────────────────────────────────────
           EMPLOYER ROUTES
        ───────────────────────────────────────── */
        Route::middleware('role:employer')->group(function () {
            
            // Employer profile setup (if needed)
            Route::get('/employer/create', [EmployerController::class, 'create'])
                ->name('employer.create');
            Route::post('/employer', [EmployerController::class, 'store'])
                ->name('employer.store');

            // My jobs (CRUD)
            Route::get('/my-jobs', [MyJobController::class, 'index'])->name('my-jobs.index');
            Route::get('/my-jobs/create', [MyJobController::class, 'create'])->name('my-jobs.create');
            Route::post('/my-jobs', [MyJobController::class, 'store'])->name('my-jobs.store');
            Route::get('/my-jobs/{myJob}/edit', [MyJobController::class, 'edit'])->name('my-jobs.edit');
            Route::put('/my-jobs/{myJob}', [MyJobController::class, 'update'])->name('my-jobs.update');
            Route::delete('/my-jobs/{myJob}', [MyJobController::class, 'destroy'])->name('my-jobs.destroy');
        });

        /* ─────────────────────────────────────────
           ADMIN ROUTES
        ───────────────────────────────────────── */
        Route::middleware('role:admin')->group(function () {
            
            // Admin can manage all jobs
            Route::get('/admin/jobs', [JobController::class, 'index'])->name('admin.jobs.index');
            Route::get('/admin/jobs/create', [JobController::class, 'create'])->name('jobs.create');
            Route::post('/admin/jobs', [JobController::class, 'store'])->name('jobs.store');
            Route::get('/admin/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');
            Route::put('/admin/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
            Route::delete('/admin/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');

            // Admin can view all applications, users, etc. (add more as needed)
        });
    });
});