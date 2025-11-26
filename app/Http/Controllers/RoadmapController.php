<?php

namespace App\Http\Controllers;

use App\Models\RoadmapTopic;
use App\Models\RoadmapStep;
use App\Models\RoadmapNote;
use Illuminate\Http\Request;

class RoadmapController extends Controller
{
    // Topics
    public function indexTopics()
    {
        $topics = RoadmapTopic::withCount('steps')->with('steps')->get();
        return response()->json($topics);
    }

    public function storeTopic(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        $topic = RoadmapTopic::create($request->all());
        return response()->json($topic, 201);
    }

    public function showTopic($id)
    {
        $topic = RoadmapTopic::with(['steps' => function($q) {
            $q->orderBy('order');
        }, 'notes'])->findOrFail($id);
        return response()->json($topic);
    }

    public function updateTopic(Request $request, $id)
    {
        $topic = RoadmapTopic::findOrFail($id);
        $topic->update($request->all());
        return response()->json($topic);
    }

    public function destroyTopic($id)
    {
        RoadmapTopic::destroy($id);
        return response()->json(null, 204);
    }

    // Steps
    public function storeStep(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:roadmap_topics,id',
            'title' => 'required|string',
        ]);

        $step = RoadmapStep::create($request->all());
        return response()->json($step, 201);
    }

    public function updateStep(Request $request, $id)
    {
        $step = RoadmapStep::findOrFail($id);
        $step->update($request->all());
        return response()->json($step);
    }

    public function destroyStep($id)
    {
        RoadmapStep::destroy($id);
        return response()->json(null, 204);
    }

    // Notes
    public function storeNote(Request $request)
    {
        $request->validate([
            'topic_id' => 'required|exists:roadmap_topics,id',
            'content' => 'required|string',
        ]);

        // Delete existing note for this topic and create new one
        RoadmapNote::where('topic_id', $request->topic_id)->delete();
        $note = RoadmapNote::create($request->all());
        return response()->json($note, 201);
    }

    public function updateNote(Request $request, $id)
    {
        $note = RoadmapNote::findOrFail($id);
        $note->update($request->all());
        return response()->json($note);
    }
}
