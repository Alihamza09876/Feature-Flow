<?php

namespace App\Http\Controllers;

use App\Models\Tool;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tool::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->has('category') && $request->category !== 'All') {
            $query->where('category', $request->category);
        }

        $tools = $query->orderBy('created_at', 'desc')->get();

        return response()->json($tools);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url',
            'category' => 'nullable|string',
        ]);

        $tool = Tool::create($request->all());

        return response()->json($tool, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tool = Tool::findOrFail($id);
        return response()->json($tool);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tool = Tool::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url',
        ]);

        $tool->update($request->all());

        return response()->json($tool);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tool = Tool::findOrFail($id);
        $tool->delete();

        return response()->json(null, 204);
    }

    public function export(Request $request)
    {
        $range = $request->query('range', '1d');
        $query = Tool::query();

        switch ($range) {
            case '1d':
                $query->whereDate('created_at', now()->toDateString());
                break;
            case '1m':
                $query->where('created_at', '>=', now()->subMonth());
                break;
            case '2m':
                $query->where('created_at', '>=', now()->subMonths(2));
                break;
            case '3m':
                $query->where('created_at', '>=', now()->subMonths(3));
                break;
        }

        $tools = $query->orderBy('created_at', 'desc')->get();

        $csvFileName = 'tools_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($tools) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Name', 'Description', 'URL', 'Category', 'Created At']);

            foreach ($tools as $tool) {
                fputcsv($file, [
                    $tool->id,
                    $tool->name,
                    $tool->description,
                    $tool->url,
                    $tool->category,
                    $tool->created_at
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
