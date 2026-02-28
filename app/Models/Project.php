<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    public const STATUS_OPTIONS = [
        'New',
        'On',
        'Pause',
        'Waiting For Approval',
        'Completed',
        'Ticked Task',
    ];

    public const STATUS_COLORS = [
        'New' => '#D3D3D3',
        'On' => '#00FF00',
        'Pause' => '#FFA500',
        'Waiting For Approval' => '#8B5CF6',
        'Completed' => '#22C55E',
        'Ticked Task' => '#3B82F6',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'assigned_to',
        'start_date',
        'end_date',
        'status',
        'status_changed_at',
        'status_before_pause',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'status_changed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusChanges(): HasMany
    {
        return $this->hasMany(ProjectStatusChange::class)->orderBy('changed_at');
    }
}
