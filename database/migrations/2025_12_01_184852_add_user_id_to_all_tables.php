<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add user_id to categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->default(1)->constrained('users')->onDelete('cascade');
            $table->dropUnique(['name']); // Remove unique constraint on name alone
            $table->unique(['user_id', 'name']); // Make name unique per user
        });

        // Add user_id to transactions table
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->default(1)->constrained('users')->onDelete('cascade');
        });

        // Add user_id to plans table
        Schema::table('plans', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->default(1)->constrained('users')->onDelete('cascade');
        });

        // Add user_id to interview_questions table
        Schema::table('interview_questions', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->default(1)->constrained('users')->onDelete('cascade');
        });

        // Add user_id to tools table
        Schema::table('tools', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->default(1)->constrained('users')->onDelete('cascade');
        });

        // Add user_id to roadmap_topics table
        Schema::table('roadmap_topics', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->default(1)->constrained('users')->onDelete('cascade');
        });

        // Add user_id to programmings table
        Schema::table('programmings', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->default(1)->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'name']);
            $table->unique(['name']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('plans', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('interview_questions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('tools', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('roadmap_topics', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('programmings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
