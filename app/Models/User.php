<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_picture',
        'salutation',
        'first_name',
        'last_name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'postcode',
        'country',
        'terms_accepted_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'terms_accepted_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /* ───── Role Helpers ───── */
    public function isAdmin(): bool     { return $this->role === 'admin'; }
    public function isConsultant(): bool{ return $this->role === 'consultant'; }
    public function isEmployer(): bool  { return $this->role === 'employer'; }
    public function isCandidate(): bool { return $this->role === 'candidate'; }
    public function hasRole(string $role): bool { return $this->role === $role; }

    /* ───── Relationships ───── */

    // Employer contacts: a user can belong to many employers (companies)
    public function employers(): BelongsToMany
    {
        return $this->belongsToMany(Employer::class, 'employer_user')
            ->withPivot(['position', 'permission_level'])
            ->withTimestamps();
    }

    // Candidate profile (moved from users table)
    public function candidateProfile(): HasOne
    {
        return $this->hasOne(CandidateProfile::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    // Candidate visibility: jobs assigned to this candidate
    public function assignedJobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_user', 'user_id', 'job_id')->withTimestamps();
    }

    /* ───── Helpers ───── */
    public function hasAcceptedTerms(): bool
    {
        return !is_null($this->terms_accepted_at);
    }

    public function getFullNameAttribute(): ?string
    {
        if ($this->first_name && $this->last_name) {
            return trim("{$this->first_name} {$this->last_name}");
        }
        return $this->name;
    }

    // Auto populate name
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($user) {
            if ($user->first_name && $user->last_name) {
                $user->name = "{$user->first_name} {$user->last_name}";
            }
        });
    }

    public function primaryEmployer(): ?Employer
    {
        return $this->employers()->first();
    }
}
