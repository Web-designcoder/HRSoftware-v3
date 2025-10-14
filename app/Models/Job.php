<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'consultant_id', // ✅ added new field
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
        // HR/admin user assigned to manage this job campaign
        return $this->belongsTo(User::class, 'consultant_id');
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
                            $q->where('company_name', 'like', "%{$search}%")
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
}
