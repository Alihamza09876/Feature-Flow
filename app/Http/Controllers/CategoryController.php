<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return response()->json(Category::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'  => 'required|string|unique:categories,name',
            'icon'  => 'nullable|string',
            'color' => 'nullable|string',
        ]);

        $category = Category::create($validated);
        return response()->json([
            'message' => 'Category created successfully',
            'data'    => $category,
        ], 201);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
