<?php

namespace App\Http\Controllers;

use App\Models\InterviewQuestion;
use Illuminate\Http\Request;

class InterviewQuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = InterviewQuestion::query();

        if ($request->has('language')) {
            $query->where('language', $request->language);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
            });
        }

        $questions = $query->get();

        return response()->json($questions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'language' => 'required|string',
            'question' => 'required|string',
            'answer' => 'required|string',
        ]);

        $question = InterviewQuestion::create($request->all());

        return response()->json($question, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $question = InterviewQuestion::findOrFail($id);
        return response()->json($question);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $question = InterviewQuestion::findOrFail($id);
        $question->update($request->all());
        return response()->json($question);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $question = InterviewQuestion::findOrFail($id);
        $question->delete();

        return response()->json(null, 204);
    }

    public function export(Request $request)
    {
        $range = $request->query('range', '1d');
        $query = InterviewQuestion::query();

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

        $questions = $query->orderBy('created_at', 'desc')->get();

        $csvFileName = 'interview_questions_export_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use ($questions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Language', 'Question', 'Answer', 'Created At']);

            foreach ($questions as $question) {
                fputcsv($file, [
                    $question->id,
                    $question->language,
                    $question->question,
                    $question->answer,
                    $question->created_at
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
