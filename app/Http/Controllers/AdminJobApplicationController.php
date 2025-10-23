<?php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminJobApplicationController extends Controller
{
    /**
     * List applications (admin = all, consultant = assigned only)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Base query with all relationships
        $query = JobApplication::query()
            ->with(['job.employer', 'job.consultant', 'user'])
            ->latest();

        // Consultants only see their assigned jobs
        if ($user->isConsultant()) {
            $query->whereHas('job', function ($q) use ($user) {
                $q->where('consultant_id', $user->id);
            });
        }

        // Optional filters
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhereHas('job', fn($j) => $j->where('title', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $applications = $query->paginate(15);

        return view('admin.applications.index', compact('applications'));
    }

    /**
     * Show "create application" form for a specific job.
     * Admins can create for any job; consultants only for jobs they manage.
     */
    public function create(Job $job)
    {
        $user = auth()->user();

        // Authorize: admin OR (consultant assigned to this job)
        if (!($user->isAdmin() || ($user->isConsultant() && (int)$job->consultant_id === (int)$user->id))) {
            abort(403, 'Unauthorized access.');
        }

        // List of candidates to choose from
        $candidates = User::where('role', 'candidate')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.applications.create', [
            'job' => $job,
            'candidates' => $candidates,
        ]);
    }

    /**
     * Store application on behalf of a candidate.
     */
    public function store(Request $request, Job $job)
    {
        $user = auth()->user();

        // Authorize: admin OR (consultant assigned to this job)
        if (!($user->isAdmin() || ($user->isConsultant() && (int)$job->consultant_id === (int)$user->id))) {
            abort(403, 'Unauthorized access.');
        }

        // Validate
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'attention_to_detail' => ['nullable', 'string', 'max:5000'],
            'customer_management' => ['nullable', 'string', 'max:5000'],
            'market_understanding' => ['nullable', 'string', 'max:5000'],
            'sales_and_business_development' => ['nullable', 'string', 'max:5000'],
            'ambition' => ['nullable', 'string', 'max:5000'],
            'leadership_skills' => ['nullable', 'string', 'max:5000'],
            'risk_assessment' => ['nullable', 'string', 'max:5000'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'video_intro' => ['nullable', 'file', 'mimetypes:video/mp4,video/quicktime,video/x-msvideo,video/x-matroska', 'max:51200'], // up to ~50MB
        ]);

        // Ensure selected user is a candidate
        $candidate = User::where('id', $validated['user_id'])
            ->where('role', 'candidate')
            ->first();

        if (!$candidate) {
            return back()->withErrors(['user_id' => 'Selected user must be a candidate.'])->withInput();
        }

        // Prevent duplicate application for same job
        $alreadyApplied = JobApplication::where('job_id', $job->id)
            ->where('user_id', $candidate->id)
            ->exists();

        if ($alreadyApplied) {
            return back()->withErrors(['user_id' => 'This candidate has already applied to this job.'])->withInput();
        }

        // Handle uploads
        $cvPath = null;
        $videoPath = null;

        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('uploads/cv', 'public');
        }
        if ($request->hasFile('video_intro')) {
            $videoPath = $request->file('video_intro')->store('uploads/videos', 'public');
        }

        // Create the application (copy a few basics from candidate profile if available)
        $application = new JobApplication();
        $application->job_id = $job->id;
        $application->user_id = $candidate->id;
        $application->first_name = $candidate->first_name ?? null;
        $application->last_name = $candidate->last_name ?? null;
        $application->city = $candidate->city ?? null;
        $application->postcode = $candidate->postcode ?? null;

        $application->attention_to_detail = $validated['attention_to_detail'] ?? null;
        $application->customer_management = $validated['customer_management'] ?? null;
        $application->market_understanding = $validated['market_understanding'] ?? null;
        $application->sales_and_business_development = $validated['sales_and_business_development'] ?? null;
        $application->ambition = $validated['ambition'] ?? null;
        $application->leadership_skills = $validated['leadership_skills'] ?? null;
        $application->risk_assessment = $validated['risk_assessment'] ?? null;

        if ($cvPath) $application->cv_path = $cvPath;
        if ($videoPath) $application->video_intro = $videoPath;

        // default status
        $application->status = 'pending';

        $application->save();

        return redirect()
            ->route('admin.job.application.show', [$job->id, $application->id])
            ->with('success', 'Application created successfully.');
    }
}
