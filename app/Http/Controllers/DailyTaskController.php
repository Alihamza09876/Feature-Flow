<?php

namespace App\Http\Controllers;

use App\Models\DailyTask;
use Illuminate\Http\Request;

class DailyTaskController extends Controller
{
    /**
     * Display a listing of the tasks (optionally by date).
     */
    public function index(Request $request)
    {
        $query = DailyTask::where('user_id', 1)
            ->orderBy('task_date', 'desc')
            ->orderBy('start_time', 'asc');

        if ($request->has('date')) {
            $query->where('task_date', $request->date);
        }

        $tasks = $query->get();

        return response()->json($tasks);
    }

    /**
     * Store a newly created task.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_date' => 'nullable|date',
        ]);

        $task = DailyTask::create([
            'user_id' => 1,
            'title' => $request->title,
            'description' => $request->description,
            'task_date' => $request->task_date ?? now()->toDateString(),
            'start_time' => now()->toTimeString(),
            'status' => 'pending',
        ]);

        return response()->json($task, 201);
    }

    /**
     * Display a single task.
     */
    public function show($id)
    {
        $task = DailyTask::where('user_id', 1)->findOrFail($id);

        return response()->json($task);
    }

    /**
     * Update the specified task.
     */
    public function update(Request $request, $id)
    {
        $task = DailyTask::where('user_id', 1)->findOrFail($id);

        $request->validate([
            'title' => 'string|max:255',
            'description' => 'nullable|string',
            'task_date' => 'date',
        ]);

        $task->update($request->only([
            'title',
            'description',
            'task_date',
        ]));

        return response()->json([
            'message' => 'Task updated successfully',
            'task' => $task
        ]);
    }

    /**
     * Remove the specified task from storage.
     */
    public function destroy($id)
    {
        $task = DailyTask::where('user_id', 1)->findOrFail($id);

        $task->delete();

        return response()->json([
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Mark task as completed.
     */
    public function complete($id)
    {
        $task = DailyTask::where('user_id', 1)->findOrFail($id);
        $task->status = $task->status === 'completed' ? 'pending' : 'completed';
        $task->save();

        return response()->json($task);
    }

    public function export(Request $request)
    {
        $range = $request->query('range', '1d');
        $query = DailyTask::where('user_id', 1);

        switch ($range) {
            case '1d':
                $query->whereDate('task_date', now()->toDateString());
                break;
            case '1m':
                $query->where('task_date', '>=', now()->subMonth()->toDateString());
                break;
            case '2m':
                $query->where('task_date', '>=', now()->subMonths(2)->toDateString());
                break;
            case '3m':
                $query->where('task_date', '>=', now()->subMonths(3)->toDateString());
                break;
        }

        $tasks = $query->orderBy('task_date', 'desc')->get();

        $csvFileName = 'tasks_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($tasks) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Description', 'Date', 'Start Time', 'Status']);

            foreach ($tasks as $task) {
                fputcsv($file, [
                    $task->id,
                    $task->title,
                    $task->description,
                    $task->task_date,
                    $task->start_time,
                    $task->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
