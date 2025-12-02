<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with('category')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();
        return response()->json($transactions);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();

        $transaction = Transaction::create($validated);

        return response()->json([
            'message' => 'Transaction created successfully!',
            'data' => $transaction->load('category'),
        ]);
    }

    public function show(Transaction $transaction)
    {
        // Ensure the transaction belongs to the authenticated user
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }
        return response()->json($transaction->load('category'));
    }


    public function update(Request $request, Transaction $transaction)
    {
        // Ensure the transaction belongs to the authenticated user
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0',
            'note' => 'nullable|string',
        ]);

        $transaction->update($validated);

        return response()->json([
            'message' => 'Transaction updated successfully!',
            'data' => $transaction->load('category'),
        ]);
    }

    public function destroy(Transaction $transaction)
    {
        // Ensure the transaction belongs to the authenticated user
        if ($transaction->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully!...',
        ]);
    }
}
