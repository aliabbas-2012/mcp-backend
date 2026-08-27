<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'description',
        'status',
        'priority',
        'assignee',
        'tags',
        'due_date',
        'position',
        'points',
        'subtasks',
    ];

    protected $casts = [
        'tags' => 'array',
        'subtasks' => 'array',
        'position' => 'integer',
        'points' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($task) {
            if (empty($task->id)) {
                $task->id = 'task-' . Str::lower(Str::random(8));
            }
            if (!isset($task->position)) {
                $maxPos = static::where('status', $task->status ?? 'todo')->max('position');
                $task->position = ($maxPos !== null) ? $maxPos + 1 : 0;
            }
        });
    }
}
