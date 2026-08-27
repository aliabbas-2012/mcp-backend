<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'task_id',
        'action',
        'actor',
        'details',
        'execution_ms',
        'created_at',
    ];

    protected $casts = [
        'execution_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    public static function log(string $action, ?string $taskId, string $details, string $actor = 'USER', int $executionMs = 10)
    {
        return static::create([
            'task_id' => $taskId,
            'action' => $action,
            'actor' => in_array(strtoupper($actor), ['USER', 'AI_MCP']) ? strtoupper($actor) : 'USER',
            'details' => $details,
            'execution_ms' => $executionMs,
            'created_at' => now(),
        ]);
    }
}
