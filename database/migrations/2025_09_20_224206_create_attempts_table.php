<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttemptsTable extends Migration
{
    public function up()
    {
        Schema::create('attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->text('submitted_code');
            $table->json('test_results')->nullable(); // JSON with test case results
            $table->boolean('is_successful')->default(false);
            $table->integer('score')->default(0);
            $table->integer('execution_time')->nullable(); // milliseconds
            $table->integer('memory_used')->nullable(); // KB
            $table->text('error_message')->nullable();
            $table->json('ai_feedback')->nullable(); // AI-generated feedback
            $table->integer('hints_used')->default(0);
            $table->integer('time_spent')->default(0); // seconds
            $table->timestamps();
            
            $table->index(['user_id', 'challenge_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('attempts');
    }
};
