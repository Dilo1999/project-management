<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectInterruption extends Model
{
    protected $fillable = [
        'project_id',
        'interrupting_project_id',
        'from_date',
        'to_date',
    ];

    protected function casts(): array
    {
        return [
            'from_date' => 'date',
            'to_date' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function interruptingProject(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'interrupting_project_id');
    }
}
