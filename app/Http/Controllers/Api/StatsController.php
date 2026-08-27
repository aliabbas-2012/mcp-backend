<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\ActivityLog;

class StatsController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        $total = $tasks->count();

        $byStatus = [
            'todo' => $tasks->where('status', 'todo')->count(),
            'in_progress' => $tasks->where('status', 'in_progress')->count(),
            'in_review' => $tasks->where('status', 'in_review')->count(),
            'done' => $tasks->where('status', 'done')->count(),
        ];

        $byPriority = [
            'low' => $tasks->where('priority', 'low')->count(),
            'medium' => $tasks->where('priority', 'medium')->count(),
            'high' => $tasks->where('priority', 'high')->count(),
            'urgent' => $tasks->where('priority', 'urgent')->count(),
        ];

        $totalPoints = $tasks->sum('points');
        $completedPoints = $tasks->where('status', 'done')->sum('points');
        $progress = $totalPoints > 0 ? round(($completedPoints / $totalPoints) * 100) : 0;

        return response()->json([
            'success' => true,
            'stats' => [
                'total_tasks' => $total,
                'total_points' => $totalPoints,
                'completed_points' => $completedPoints,
                'progress_percentage' => $progress,
                'by_status' => $byStatus,
                'by_priority' => $byPriority,
                'urgent_tasks_count' => $byPriority['urgent'],
                'recent_activity_count' => ActivityLog::count(),
            ],
        ]);
    }
}
