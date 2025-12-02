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
            'data' => Plan::where('user_id', auth()->id())->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'type' => 'required',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();

        $plan = Plan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully!',
            'data' => $plan
        ], 201);
    }

    public function show(Plan $plan)
    {
        // Ensure the plan belongs to the authenticated user
        if ($plan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        // Ensure the plan belongs to the authenticated user
        if ($plan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

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
        // Ensure the plan belongs to the authenticated user
        if ($plan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully!'
        ]);
    }

    public function complete(Plan $plan)
    {
        // Ensure the plan belongs to the authenticated user
        if ($plan->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $plan->update(['is_completed' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Plan marked as completed!',
            'data' => $plan
        ]);
    }
}
