<?php
namespace App\Http\Controllers;

use App\Models\Programmings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgrammingController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Programmings::all(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $programming = Programmings::create($validated);

        return response()->json($programming, 201);
    }

    public function show($id): JsonResponse
    {
        $programming = Programmings::find($id);

        if (! $programming) {
            return response()->json(['message' => 'Programmings not found'], 404);
        }

        return response()->json($programming, 200);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $programming = Programmings::find($id);

        if (! $programming) {
            return response()->json(['message' => 'Programmings not found'], 404);
        }

        $validated = $request->validate([
            'title'   => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        $programming->update($validated);

        return response()->json($programming, 200);
    }

    public function destroy($id): JsonResponse
    {
        $programming = Programmings::find($id);

        if (! $programming) {
            return response()->json(['message' => 'Programmings not found'], 404);
        }

        $programming->delete();

        return response()->json(['message' => 'Programmings deleted'], 200);
    }
}
