<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobRequiredDocument extends Model
{
    protected $fillable = ['job_id', 'name', 'path', 'sort_order'];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    protected $appends = ['url'];

    public function getUrlAttribute(): ?string
    {
        return $this->path ? asset('storage/'.$this->path) : null;
    }
}
