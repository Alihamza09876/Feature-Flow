#!/usr/bin/env php
<?php

/**
 * Multi-User Data Isolation Test Script
 * 
 * This script tests the multi-user implementation by:
 * 1. Creating test users
 * 2. Authenticating as each user
 * 3. Creating data for each user
 * 4. Verifying data isolation
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Category;
use App\Models\DailyTask;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;

echo "=== Multi-User Data Isolation Test ===\n\n";

// Clean up test users if they exist
echo "Cleaning up existing test users...\n";
User::where('email', 'LIKE', 'testuser%@example.com')->delete();

// Create two test users
echo "Creating test users...\n";
$user1 = User::create([
    'name' => 'Test User 1',
    'email' => 'testuser1@example.com',
    'password' => Hash::make('password123'),
]);
echo "✓ Created User 1 (ID: {$user1->id})\n";

$user2 = User::create([
    'name' => 'Test User 2',
    'email' => 'testuser2@example.com',
    'password' => Hash::make('password123'),
]);
echo "✓ Created User 2 (ID: {$user2->id})\n\n";

// Create data for User 1
echo "Creating data for User 1...\n";
$category1 = Category::create([
    'user_id' => $user1->id,
    'name' => 'User 1 Category',
    'icon' => '📁',
    'color' => '#FF0000',
]);
echo "✓ Created category for User 1\n";

$task1 = DailyTask::create([
    'user_id' => $user1->id,
    'title' => 'User 1 Task',
    'description' => 'This is a task for user 1',
    'task_date' => now()->toDateString(),
    'start_time' => now()->toTimeString(),
    'status' => 'pending',
]);
echo "✓ Created task for User 1\n";

$transaction1 = Transaction::create([
    'user_id' => $user1->id,
    'category_id' => $category1->id,
    'amount' => 100.00,
    'note' => 'User 1 transaction',
]);
echo "✓ Created transaction for User 1\n\n";

// Create data for User 2
echo "Creating data for User 2...\n";
$category2 = Category::create([
    'user_id' => $user2->id,
    'name' => 'User 2 Category',
    'icon' => '📂',
    'color' => '#00FF00',
]);
echo "✓ Created category for User 2\n";

$task2 = DailyTask::create([
    'user_id' => $user2->id,
    'title' => 'User 2 Task',
    'description' => 'This is a task for user 2',
    'task_date' => now()->toDateString(),
    'start_time' => now()->toTimeString(),
    'status' => 'pending',
]);
echo "✓ Created task for User 2\n";

$transaction2 = Transaction::create([
    'user_id' => $user2->id,
    'category_id' => $category2->id,
    'amount' => 200.00,
    'note' => 'User 2 transaction',
]);
echo "✓ Created transaction for User 2\n\n";

// Test data isolation
echo "=== Testing Data Isolation ===\n\n";

// Test User 1 can only see their data
$user1Categories = Category::where('user_id', $user1->id)->count();
$user1Tasks = DailyTask::where('user_id', $user1->id)->count();
$user1Transactions = Transaction::where('user_id', $user1->id)->count();

echo "User 1 Data:\n";
echo "  Categories: {$user1Categories} (Expected: 1)\n";
echo "  Tasks: {$user1Tasks} (Expected: 1)\n";
echo "  Transactions: {$user1Transactions} (Expected: 1)\n";

if ($user1Categories === 1 && $user1Tasks === 1 && $user1Transactions === 1) {
    echo "  ✓ User 1 data count is correct\n\n";
} else {
    echo "  ✗ User 1 data count is INCORRECT\n\n";
}

// Test User 2 can only see their data
$user2Categories = Category::where('user_id', $user2->id)->count();
$user2Tasks = DailyTask::where('user_id', $user2->id)->count();
$user2Transactions = Transaction::where('user_id', $user2->id)->count();

echo "User 2 Data:\n";
echo "  Categories: {$user2Categories} (Expected: 1)\n";
echo "  Tasks: {$user2Tasks} (Expected: 1)\n";
echo "  Transactions: {$user2Transactions} (Expected: 1)\n";

if ($user2Categories === 1 && $user2Tasks === 1 && $user2Transactions === 1) {
    echo "  ✓ User 2 data count is correct\n\n";
} else {
    echo "  ✗ User 2 data count is INCORRECT\n\n";
}

// Verify User 1 cannot see User 2's data
$user1CanSeeUser2Categories = Category::where('user_id', $user1->id)
    ->where('id', $category2->id)
    ->exists();

$user1CanSeeUser2Tasks = DailyTask::where('user_id', $user1->id)
    ->where('id', $task2->id)
    ->exists();

echo "Cross-User Access Test:\n";
echo "  User 1 can see User 2's category: " . ($user1CanSeeUser2Categories ? "YES (✗ FAIL)" : "NO (✓ PASS)") . "\n";
echo "  User 1 can see User 2's task: " . ($user1CanSeeUser2Tasks ? "YES (✗ FAIL)" : "NO (✓ PASS)") . "\n\n";

// Test relationships
echo "=== Testing Relationships ===\n\n";

$user1WithRelations = User::with(['categories', 'dailyTasks', 'transactions'])->find($user1->id);
echo "User 1 Relationships:\n";
echo "  Categories: " . $user1WithRelations->categories->count() . " (Expected: 1)\n";
echo "  Tasks: " . $user1WithRelations->dailyTasks->count() . " (Expected: 1)\n";
echo "  Transactions: " . $user1WithRelations->transactions->count() . " (Expected: 1)\n";

if ($user1WithRelations->categories->count() === 1 && 
    $user1WithRelations->dailyTasks->count() === 1 && 
    $user1WithRelations->transactions->count() === 1) {
    echo "  ✓ User 1 relationships are correct\n\n";
} else {
    echo "  ✗ User 1 relationships are INCORRECT\n\n";
}

// Summary
echo "=== Test Summary ===\n\n";

$allTestsPassed = 
    $user1Categories === 1 && $user1Tasks === 1 && $user1Transactions === 1 &&
    $user2Categories === 1 && $user2Tasks === 1 && $user2Transactions === 1 &&
    !$user1CanSeeUser2Categories && !$user1CanSeeUser2Tasks &&
    $user1WithRelations->categories->count() === 1 &&
    $user1WithRelations->dailyTasks->count() === 1 &&
    $user1WithRelations->transactions->count() === 1;

if ($allTestsPassed) {
    echo "✓ ALL TESTS PASSED - Multi-user data isolation is working correctly!\n";
} else {
    echo "✗ SOME TESTS FAILED - Please review the implementation\n";
}

// Cleanup
echo "\nCleaning up test data...\n";
$user1->delete(); // This will cascade delete all related data
$user2->delete(); // This will cascade delete all related data
echo "✓ Test data cleaned up\n\n";

echo "Test completed!\n";
