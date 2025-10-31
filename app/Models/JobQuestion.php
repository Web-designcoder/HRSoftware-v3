<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobQuestion extends Model
{
    protected $fillable = [
        'job_id',
        'heading',
        'body',
        'is_default',
        'is_enabled',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_enabled' => 'boolean',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
