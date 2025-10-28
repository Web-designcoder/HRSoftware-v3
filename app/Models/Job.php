<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Job extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employer_id',
        'title',
        'description',
        'assignment_overview',
        'location',
        'city',
        'country',
        'salary',
        'experience',
        'category',
        'date_posted',
        'managed_by',
        'company_logo',
        'campaign_documents',
        'consultant_id',
        // NEW:
        'terms_candidate',
        'terms_employer',
        'employer_intro_video',
        'candidate_assessment_video',
        'status',
        'primary_contact_id',
    ];

    protected $casts = [
        'date_posted' => 'date',
        'salary' => 'decimal:2',
    ];

    public static array $experience = ['entry', 'intermediate', 'senior'];
    public static array $category = ['IT', 'Finance', 'Marketing', 'Sales', 'Healthcare', 'Education', 'Other'];

    /* ───── Relationships ───── */

    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function consultant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'consultant_id');
    }

    // Visibility list: candidates allowed to see this job
    public function assignedCandidates(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_user', 'job_id', 'user_id')->withTimestamps();
    }

    // Employer contacts attached to this job (admin-managed)
    public function contacts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_contacts', 'job_id', 'user_id')->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(JobDocument::class)->orderBy('sort_order');
    }

    public function requiredDocuments(): HasMany
    {
        return $this->hasMany(JobRequiredDocument::class)->orderBy('sort_order');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(JobQuestion::class)->orderBy('sort_order');
    }

    /* ───── Scopes ───── */

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('employer', fn($q) =>
                            $q->where('name', 'like', "%{$search}%")
                        );
                });
            })
            ->when($filters['min_salary'] ?? null, fn($q, $min) =>
                $q->where('salary', '>=', $min)
            )
            ->when($filters['max_salary'] ?? null, fn($q, $max) =>
                $q->where('salary', '<=', $max)
            )
            ->when($filters['experience'] ?? null, fn($q, $exp) =>
                $q->where('experience', $exp)
            )
            ->when($filters['category'] ?? null, fn($q, $cat) =>
                $q->where('category', $cat)
            );
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if (!$user) return $query->whereRaw('1=0');

        if ($user->isAdmin()) return $query;

        if ($user->isConsultant()) {
            return $query->where('consultant_id', $user->id);
        }

        if ($user->isEmployer()) {
            return $query->whereIn('employer_id', $user->employers()->pluck('employers.id'));
        }

        if ($user->isCandidate()) {
            return $query->whereHas('assignedCandidates', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        return $query->whereRaw('1=0');
    }

    /* ───── Helpers ───── */

    public function hasUserApplied(User|int $user): bool
    {
        $userId = $user instanceof User ? $user->id : $user;

        return $this->jobApplications()
            ->where('user_id', $userId)
            ->exists();
    }

    public function getCompanyLogoUrlAttribute(): ?string
    {
        return $this->company_logo ? asset('storage/' . $this->company_logo) : null;
    }

    public function getCampaignDocumentsUrlAttribute(): ?string
    {
        return $this->campaign_documents ? asset('storage/' . $this->campaign_documents) : null;
    }

    public function getEmployerIntroVideoUrlAttribute(): ?string
    {
        return $this->employer_intro_video ? asset('storage/' . $this->employer_intro_video) : null;
    }

    public function getCandidateAssessmentVideoUrlAttribute(): ?string
    {
        return $this->candidate_assessment_video ? asset('storage/' . $this->candidate_assessment_video) : null;
    }
}
