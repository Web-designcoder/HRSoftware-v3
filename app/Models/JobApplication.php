<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'user_id',
        'first_name',
        'last_name',
        'city',
        'postcode',
        'cv_path',
        'video_intro',
        'attention_to_detail',
        'customer_management',
        'market_understanding',
        'sales_and_business_development',
        'ambition',
        'leadership_skills',
        'risk_assessment',
        'status',
    ];

    /* ───── Relationships ───── */

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /* ───── Helpers ───── */

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isReviewing(): bool
    {
        return $this->status === 'reviewing';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isWithdrawn(): bool
    {
        return $this->status === 'withdrawn';
    }

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_path ? asset('storage/' . $this->cv_path) : null;
    }

    public function getVideoUrlAttribute(): ?string
    {
        return $this->video_intro ? asset('storage/' . $this->video_intro) : null;
    }
}