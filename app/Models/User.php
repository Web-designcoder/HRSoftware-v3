<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'cv',
        'medical_check',
        'police_clearance',
        'qualifications',
        'other_files',
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
            'qualifications' => 'array',
            'other_files' => 'array',
        ];
    }

    /* ───── Role Checking Methods ───── */
    
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEmployer(): bool
    {
        return $this->role === 'employer';
    }

    public function isCandidate(): bool
    {
        return $this->role === 'candidate';
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /* ───── Relationships ───── */

    public function employer(): HasOne
    {
        return $this->hasOne(Employer::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
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
}