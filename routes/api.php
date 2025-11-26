<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\DailyTaskController;
use App\Http\Controllers\InterviewQuestionController;
use App\Http\Controllers\ProgrammingController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ToolController;
use App\Http\Controllers\RoadmapController;
use Illuminate\Support\Facades\Route;

Route::apiResource('users', UserController::class);
Route::apiResource('transactions', TransactionController::class);
Route::apiResource('programming', ProgrammingController::class);
Route::get('tasks/export', [DailyTaskController::class, 'export']);
Route::put('/tasks/{id}/complete', [DailyTaskController::class, 'complete']);
Route::apiResource('tasks', DailyTaskController::class);
Route::get('interview-questions/export', [InterviewQuestionController::class, 'export']);
Route::apiResource('interview-questions', InterviewQuestionController::class);
Route::get('tools/export', [ToolController::class, 'export']);
Route::apiResource('tools', ToolController::class);

// Roadmap routes
Route::get('roadmap/topics', [RoadmapController::class, 'indexTopics']);
Route::post('roadmap/topics', [RoadmapController::class, 'storeTopic']);
Route::get('roadmap/topics/{id}', [RoadmapController::class, 'showTopic']);
Route::put('roadmap/topics/{id}', [RoadmapController::class, 'updateTopic']);
Route::delete('roadmap/topics/{id}', [RoadmapController::class, 'destroyTopic']);
Route::post('roadmap/steps', [RoadmapController::class, 'storeStep']);
Route::put('roadmap/steps/{id}', [RoadmapController::class, 'updateStep']);
Route::delete('roadmap/steps/{id}', [RoadmapController::class, 'destroyStep']);
Route::post('roadmap/notes', [RoadmapController::class, 'storeNote']);
Route::put('roadmap/notes/{id}', [RoadmapController::class, 'updateNote']);

Route::post('/login', [UserController::class, 'login']);
Route::apiResource('categories', CategoryController::class);
Route::resource('plans', PlanController::class);
Route::patch('plans/{plan}/complete', [PlanController::class, 'complete'])->name('plans.complete');

Route::post('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
