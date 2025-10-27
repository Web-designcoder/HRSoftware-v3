<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address_line1',
        'address_line2',
        'city',
        'postcode',
        'country',
        'industry',
    ];

    /* ───── Relationships ───── */

    // Company contacts (users with role=employer, but relation is generic users)
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'employer_user')
            ->withPivot(['position', 'permission_level'])
            ->withTimestamps();
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /* ───── Helpers ───── */
    public function getCompanyLogoUrlAttribute(): ?string
    {
        // keeping for compatibility if you still upload logos into employers later
        return null;
    }
}
