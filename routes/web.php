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
use App\Http\Controllers\EmployerJobApplicationController;
use App\Http\Controllers\AdminUserController; // ← ADDED
use Illuminate\Support\Facades\Route;

/* ───────────────────────────────────────────────
   PUBLIC ROUTES - LOGIN ONLY
─────────────────────────────────────────────── */
Route::get('login', [AuthController::class, 'create'])->name('login');
Route::get('auth/create', [AuthController::class, 'create'])->name('auth.create');
Route::post('auth', [AuthController::class, 'store'])->name('auth.store');

/* ───────────────────────────────────────────────
   AUTHENTICATED ROUTES
─────────────────────────────────────────────── */
Route::middleware('auth')->group(function () {

    Route::get('/', fn() => redirect()->route('dashboard'));
    Route::delete('logout', [AuthController::class, 'destroy'])->name('logout');
    Route::delete('auth', [AuthController::class, 'destroy'])->name('auth.destroy');

    // Terms
    Route::get('/terms', [TermsController::class, 'show'])->name('terms.show');
    Route::post('/terms', [TermsController::class, 'accept'])->name('terms.accept');

    // Routes after terms accepted
    Route::middleware('terms.accepted')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Jobs (view-only)
        Route::get('/jobs', [JobController::class, 'index'])->name('jobs.index');
        Route::get('/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');

        // Account
        Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
        Route::put('/account', [AccountController::class, 'update'])->name('account.update');
        Route::post('/account/upload-attachment', [AccountController::class, 'uploadAttachment'])->name('account.upload');
        Route::delete('/account/delete-attachment', [AccountController::class, 'deleteAttachment'])->name('account.delete');

        /* ───────────────────────────────
           CANDIDATE ROUTES
        ─────────────────────────────── */
        Route::middleware('role:candidate')->group(function () {
            Route::get('/jobs/{job}/apply', [JobApplicationController::class, 'create'])->name('job.application.create');
            Route::post('/jobs/{job}/apply', [JobApplicationController::class, 'store'])->name('job.application.store');
            Route::get('/jobs/{job}/application/{jobApplication}', [JobApplicationController::class, 'show'])->name('job.application.show');
            Route::delete('/jobs/{job}/application/{jobApplication}', [JobApplicationController::class, 'destroy'])->name('job.application.destroy');
            Route::get('/my-applications', [MyJobApplicationController::class, 'index'])->name('my-job-applications.index');
            Route::delete('/my-applications/{myJobApplication}', [MyJobApplicationController::class, 'destroy'])->name('my-job-applications.destroy');
        });

        /* ───────────────────────────────
           EMPLOYER ROUTES
        ─────────────────────────────── */
        Route::middleware('role:employer')->group(function () {
            // Employer profile setup
            Route::get('/employer/create', [EmployerController::class, 'create'])->name('employer.create');
            Route::post('/employer', [EmployerController::class, 'store'])->name('employer.store');

            // ⛔ Disable employer job CRUD for now (read-only)
            Route::get('/my-jobs', [MyJobController::class, 'index'])->name('my-jobs.index');

            // Employer can view candidate applications (read-only)
            Route::get('/employer/jobs/{job}/applications/{jobApplication}', [EmployerJobApplicationController::class, 'show'])
                ->name('employer.job.application.show');
        });

        /* ───────────────────────────────
           ADMIN & CONSULTANT ROUTES
        ─────────────────────────────── */
        Route::middleware('role:admin,consultant')->group(function () {
            // Admin & Consultant: Update job application status
            Route::put('/applications/{application}/update-status', [JobApplicationController::class, 'updateStatus'])
                ->name('job.application.updateStatus');

            // ⚠️ IMPORTANT: Keep "create" route ABOVE the dynamic {jobApplication} route below!
            // Otherwise Laravel will interpret "create" as a {jobApplication} parameter and return 404.
            Route::get('/admin/jobs/{job}/applications/create', [App\Http\Controllers\AdminJobApplicationController::class, 'create'])
                ->name('admin.applications.create');

            // Store application
            Route::post('/admin/jobs/{job}/applications', [App\Http\Controllers\AdminJobApplicationController::class, 'store'])
                ->name('admin.applications.store');

            // Admin/Consultant route (reuse same controller)
            Route::get('/admin/jobs/{job}/applications/{jobApplication}', [EmployerJobApplicationController::class, 'show'])
                ->name('admin.job.application.show');

            // Admin applications list
            Route::get('/admin/applications', [App\Http\Controllers\AdminJobApplicationController::class, 'index'])
                ->name('admin.applications.index');

            // Job CRUD
            Route::get('/admin/jobs', [JobController::class, 'index'])->name('admin.jobs.index');
            Route::get('/admin/jobs/create', [JobController::class, 'create'])->name('jobs.create');
            Route::post('/admin/jobs', [JobController::class, 'store'])->name('jobs.store');
            Route::get('/admin/jobs/{job}/edit', [JobController::class, 'edit'])->name('jobs.edit');
            Route::put('/admin/jobs/{job}', [JobController::class, 'update'])->name('jobs.update');
            Route::delete('/admin/jobs/{job}', [JobController::class, 'destroy'])->name('jobs.destroy');
        });

        /* ───────────────────────────────
           ADMIN (SUPERADMIN) — USERS CRUD
        ─────────────────────────────── */
        Route::middleware('role:admin')->prefix('/admin/users')->name('admin.users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
        });
    });
});
