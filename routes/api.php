<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\StatsController;
use App\Http\Controllers\Api\ActivityLogController;
use App\Http\Controllers\Api\DemoResetController;

Route::prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'index']);
    Route::post('/', [TaskController::class, 'store']);
    Route::post('/bulk', [TaskController::class, 'bulkStore']);
    Route::get('/{id}', [TaskController::class, 'show']);
    Route::put('/{id}', [TaskController::class, 'update']);
    Route::patch('/{id}', [TaskController::class, 'update']);
    Route::patch('/{id}/move', [TaskController::class, 'move']);
    Route::delete('/{id}', [TaskController::class, 'destroy']);
});

Route::get('/stats', [StatsController::class, 'index']);
Route::get('/activity', [ActivityLogController::class, 'index']);
Route::post('/reset', [DemoResetController::class, 'reset']);

// MCP Discovery endpoint for LLMs & OpenAPI Clients
Route::get('/mcp/tools', function () {
    return response()->json([
        'success' => true,
        'protocol' => 'mcp-1.0',
        'tools' => [
            [
                'name' => 'list_tasks',
                'description' => 'List tasks on the Kanban board with optional filters (status, priority, assignee, search)',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'status' => ['type' => 'string', 'enum' => ['todo', 'in_progress', 'in_review', 'done']],
                        'priority' => ['type' => 'string', 'enum' => ['low', 'medium', 'high', 'urgent']],
                        'assignee' => ['type' => 'string'],
                        'search' => ['type' => 'string'],
                    ],
                ],
            ],
            [
                'name' => 'create_task',
                'description' => 'Create a new task on the Kanban board and save to database',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'title' => ['type' => 'string'],
                        'description' => ['type' => 'string'],
                        'status' => ['type' => 'string', 'enum' => ['todo', 'in_progress', 'in_review', 'done']],
                        'priority' => ['type' => 'string', 'enum' => ['low', 'medium', 'high', 'urgent']],
                        'assignee' => ['type' => 'string'],
                    ],
                    'required' => ['title'],
                ],
            ],
            [
                'name' => 'update_task',
                'description' => 'Update fields (title, priority, assignee, status, due_date) of an existing task',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'string'],
                        'title' => ['type' => 'string'],
                        'priority' => ['type' => 'string', 'enum' => ['low', 'medium', 'high', 'urgent']],
                        'assignee' => ['type' => 'string'],
                        'status' => ['type' => 'string', 'enum' => ['todo', 'in_progress', 'in_review', 'done']],
                    ],
                    'required' => ['id'],
                ],
            ],
            [
                'name' => 'move_task_status',
                'description' => 'Move a task to a different Kanban column (todo, in_progress, in_review, done)',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'string'],
                        'status' => ['type' => 'string', 'enum' => ['todo', 'in_progress', 'in_review', 'done']],
                    ],
                    'required' => ['id', 'status'],
                ],
            ],
            [
                'name' => 'delete_task',
                'description' => 'Delete a task permanently from the Kanban board',
                'parameters' => [
                    'type' => 'object',
                    'properties' => [
                        'id' => ['type' => 'string'],
                    ],
                    'required' => ['id'],
                ],
            ],
            [
                'name' => 'get_board_summary',
                'description' => 'Get aggregated statistics and status counts across the entire Kanban board',
                'parameters' => ['type' => 'object', 'properties' => (object)[]],
            ],
        ],
    ]);
});
