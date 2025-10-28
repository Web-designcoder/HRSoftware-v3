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
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminJobApplicationController;
use App\Http\Controllers\Admin\JobContactController;
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
            Route::get('/employer/{employer}', [EmployerController::class, 'show'])->name('employer.show');


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

            // Job CRUD (dedicated Admin controller)
            Route::prefix('admin')->name('admin.')->group(function () {
                Route::resource('jobs', \App\Http\Controllers\Admin\JobController::class);
            });

            Route::get('admin/applications/create', [AdminJobApplicationController::class, 'createStandalone'])
                ->name('admin.applications.createStandalone');

            Route::post('admin/applications/store', [AdminJobApplicationController::class, 'storeStandalone'])
                ->name('admin.applications.storeStandalone');

            /* ───────────────────────────────
            ADMIN JOB CANDIDATE MANAGEMENT
            ─────────────────────────────── */
            Route::prefix('admin/jobs/{job}')->name('admin.jobs.')->group(function () {
                Route::post('candidates/attach', [App\Http\Controllers\Admin\JobCandidateController::class, 'attach'])
                    ->name('candidates.attach');
                Route::delete('candidates/{candidate}', [App\Http\Controllers\Admin\JobCandidateController::class, 'detach'])
                    ->name('candidates.detach');
                Route::patch('candidates/{candidate}/status', [App\Http\Controllers\Admin\JobCandidateController::class, 'updateStatus'])
                    ->name('candidates.status');
            });

            /* ───────────────────────────────
            ADMIN JOB AJAX UPDATES (Details, Overviews, Logo)
            ─────────────────────────────── */
            Route::prefix('admin/jobs/{job}')->name('admin.jobs.')->group(function () {
                Route::patch('details', [\App\Http\Controllers\Admin\JobUpdateController::class, 'updateDetails'])
                    ->name('details.update');
                Route::patch('overviews', [\App\Http\Controllers\Admin\JobUpdateController::class, 'updateOverviews'])
                    ->name('overviews.update');
                Route::post('logo', [\App\Http\Controllers\Admin\JobUpdateController::class, 'uploadLogo'])
                    ->name('logo.upload');
            });
            
            Route::prefix('admin/jobs/{job}/contacts')->name('admin.jobs.contacts.')->group(function () {
                Route::get('/', [JobContactController::class, 'index'])->name('index');
                Route::post('attach', [JobContactController::class, 'attach'])->name('attach');
                Route::delete('{user}', [JobContactController::class, 'detach'])->name('detach');
                Route::patch('primary', [JobContactController::class, 'setPrimary'])->name('primary');
            });
            
            Route::get('/admin/users/clients/json', [AdminUserController::class, 'clientsJson'])
                ->name('admin.users.clients.json');

            // ====== ADMIN JOB EXTRA AJAX (Videos, Documents, Required Docs, Questions, Terms) ======
            Route::prefix('admin/jobs/{job}')->name('admin.jobs.')->group(function () {
                // Videos
                Route::post('video/employer-intro', [\App\Http\Controllers\Admin\JobUpdateController::class, 'uploadEmployerIntroVideo'])->name('video.employer.upload');
                Route::delete('video/employer-intro', [\App\Http\Controllers\Admin\JobUpdateController::class, 'deleteEmployerIntroVideo'])->name('video.employer.delete');
                Route::post('video/candidate-assessment', [\App\Http\Controllers\Admin\JobUpdateController::class, 'uploadCandidateAssessmentVideo'])->name('video.candidate.upload');
                Route::delete('video/candidate-assessment', [\App\Http\Controllers\Admin\JobUpdateController::class, 'deleteCandidateAssessmentVideo'])->name('video.candidate.delete');

                // Campaign Documents
                Route::get('documents', [\App\Http\Controllers\Admin\JobUpdateController::class, 'documentsIndex'])->name('documents.index');
                Route::post('documents', [\App\Http\Controllers\Admin\JobUpdateController::class, 'documentsStore'])->name('documents.store');
                Route::delete('documents/{document}', [\App\Http\Controllers\Admin\JobUpdateController::class, 'documentsDestroy'])->name('documents.destroy');
                Route::patch('documents/reorder', [\App\Http\Controllers\Admin\JobUpdateController::class, 'documentsReorder'])->name('documents.reorder');

                // Required Candidate Documents
                Route::get('required-docs', [\App\Http\Controllers\Admin\JobUpdateController::class, 'reqDocsIndex'])->name('reqdocs.index');
                Route::post('required-docs', [\App\Http\Controllers\Admin\JobUpdateController::class, 'reqDocsStore'])->name('reqdocs.store');
                Route::delete('required-docs/{document}', [\App\Http\Controllers\Admin\JobUpdateController::class, 'reqDocsDestroy'])->name('reqdocs.destroy');
                Route::patch('required-docs/reorder', [\App\Http\Controllers\Admin\JobUpdateController::class, 'reqDocsReorder'])->name('reqdocs.reorder');

                // Questions
                Route::get('questions', [\App\Http\Controllers\Admin\JobUpdateController::class, 'questionsIndex'])->name('questions.index');
                Route::post('questions/seed', [\App\Http\Controllers\Admin\JobUpdateController::class, 'questionsSeedDefaults'])->name('questions.seed');
                Route::post('questions', [\App\Http\Controllers\Admin\JobUpdateController::class, 'questionsCreate'])->name('questions.store');
                Route::patch('questions/{question}/toggle', [\App\Http\Controllers\Admin\JobUpdateController::class, 'questionsToggle'])->name('questions.toggle');
                Route::delete('questions/{question}', [\App\Http\Controllers\Admin\JobUpdateController::class, 'questionsDestroy'])->name('questions.destroy');
                Route::patch('questions/reorder', [\App\Http\Controllers\Admin\JobUpdateController::class, 'questionsReorder'])->name('questions.reorder');

                // Terms
                Route::get('terms', [\App\Http\Controllers\Admin\JobUpdateController::class, 'termsGet'])->name('terms.get');
                Route::patch('terms', [\App\Http\Controllers\Admin\JobUpdateController::class, 'termsUpdate'])->name('terms.update');
            });


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
            Route::get('/candidates', [AdminUserController::class, 'candidates'])->name('candidates');
            Route::get('/clients', [AdminUserController::class, 'clients'])->name('clients');
        });

    });
});
