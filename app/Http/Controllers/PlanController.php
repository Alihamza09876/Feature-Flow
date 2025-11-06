<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => Plan::latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
        ]);

        $plan = Plan::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully!',
            'data' => $plan
        ], 201);
    }

    public function show(Plan $plan)
    {
        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
        ]);

        $plan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully!',
            'data' => $plan
        ]);
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully!'
        ]);
    }

    public function complete(Plan $plan)
    {
        $plan->update(['is_completed' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Plan marked as completed!',
            'data' => $plan
        ]);
    }
}
