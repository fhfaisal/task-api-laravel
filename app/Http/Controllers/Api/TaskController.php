<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    // GET /api/tasks - Get all tasks for logged-in user
    public function index(Request $request)
    {
        $tasks = $request->user()->tasks()->get();

        return response()->json([
            'tasks' => $tasks,
            'message' => 'Tasks retrieved successfully',
        ], 200);
    }

    // POST /api/tasks - Create a new task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
        ]);

        $task = $request->user()->tasks()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_completed' => $validated['is_completed'] ?? false,
        ]);

        return response()->json([
            'task' => $task,
            'message' => 'Task created successfully',
        ], 201);
    }

    // GET /api/tasks/{id} - Get a single task
    public function show(Request $request, $id)
    {
        $task = Task::where('user_id', $request->user()->id)->find($id);

        return response()->json([
            'task' => $task,
            'message' => 'Task retrieved successfully',
        ], 200);
    }

    // PUT /api/tasks/{id} - Update a task
    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', $request->user()->id)->find($id);
        if (! $task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
        ]);

        $task->update($validated);

        return response()->json([
            'task' => $task,
            'message' => 'Task updated successfully',
        ], 200);
    }

    // DELETE /api/tasks/{id} - Delete a task
    public function destroy(Request $request, Task $task)
    {
        // Check if task belongs to logged-in user
        if ($task->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 403);
        }

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully',
        ], 200);
    }
}
