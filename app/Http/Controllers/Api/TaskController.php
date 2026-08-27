<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assignee')) {
            $query->where('assignee', 'like', '%' . $request->assignee . '%');
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%")
                  ->orWhere('id', 'like', "%{$s}%");
            });
        }

        $tasks = $query->orderBy('position', 'asc')->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'count' => $tasks->count(),
            'tasks' => $tasks,
        ]);
    }

    public function show($id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'error' => "Task not found with ID [{$id}]",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'task' => $task,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:todo,in_progress,in_review,done',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'assignee' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'due_date' => 'nullable|date',
            'points' => 'nullable|integer',
        ]);

        $actor = $request->header('X-Actor', $request->input('actor', 'USER'));
        $task = Task::create($validated);

        ActivityLog::log(
            'CREATE',
            $task->id,
            "Created task [{$task->title}] with status [{$task->status}] and priority [{$task->priority}]",
            $actor,
            $request->input('execution_ms', 12)
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully created task \"{$task->title}\"",
            'task' => $task,
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'error' => "Task not found with ID [{$id}]",
            ], 404);
        }

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|in:todo,in_progress,in_review,done',
            'priority' => 'nullable|in:low,medium,high,urgent',
            'assignee' => 'nullable|string|max:100',
            'tags' => 'nullable|array',
            'due_date' => 'nullable|date',
            'points' => 'nullable|integer',
        ]);

        $actor = $request->header('X-Actor', $request->input('actor', 'USER'));
        $task->update(array_filter($validated, fn($v) => $v !== null));

        ActivityLog::log(
            'UPDATE',
            $task->id,
            "Updated task [{$task->id}] fields",
            $actor,
            $request->input('execution_ms', 10)
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully updated task \"{$task->title}\"",
            'task' => $task,
        ]);
    }

    public function move(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'error' => "Task not found with ID [{$id}]",
            ], 404);
        }

        $validated = $request->validate([
            'status' => 'required|in:todo,in_progress,in_review,done',
            'position' => 'nullable|integer',
        ]);

        $actor = $request->header('X-Actor', $request->input('actor', 'USER'));
        $oldStatus = $task->status;
        $task->status = $validated['status'];

        if (isset($validated['position'])) {
            $task->position = $validated['position'];
        }

        $task->save();

        ActivityLog::log(
            'MOVE',
            $task->id,
            "Moved task [{$task->id}] from [{$oldStatus}] to [{$task->status}]",
            $actor,
            $request->input('execution_ms', 8)
        );

        return response()->json([
            'success' => true,
            'message' => "Moved task \"{$task->title}\" from [{$oldStatus}] to [{$task->status}]",
            'task' => $task,
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            return response()->json([
                'success' => false,
                'error' => "Task not found with ID [{$id}]",
            ], 404);
        }

        $actor = $request->header('X-Actor', $request->input('actor', 'USER'));
        $title = $task->title;
        $task->delete();

        ActivityLog::log(
            'DELETE',
            $id,
            "Permanently deleted task [{$title}] ({$id})",
            $actor,
            $request->input('execution_ms', 9)
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted task [{$id}]",
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string',
            'tasks.*.status' => 'nullable|in:todo,in_progress,in_review,done',
            'tasks.*.priority' => 'nullable|in:low,medium,high,urgent',
            'tasks.*.assignee' => 'nullable|string',
        ]);

        $actor = $request->header('X-Actor', $request->input('actor', 'USER'));
        $created = [];

        foreach ($validated['tasks'] as $taskData) {
            $task = Task::create($taskData);
            $created[] = $task;
        }

        ActivityLog::log(
            'BULK_CREATE',
            null,
            "Bulk created " . count($created) . " tasks via AI MCP sprint breakdown",
            $actor,
            $request->input('execution_ms', 15)
        );

        return response()->json([
            'success' => true,
            'count' => count($created),
            'tasks' => $created,
        ], 201);
    }
}
