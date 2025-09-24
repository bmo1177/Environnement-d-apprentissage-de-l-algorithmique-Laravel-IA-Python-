<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChallengesTable extends Migration
{
    public function up()
    {
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('problem_statement');
            $table->foreignId('competency_id')->constrained()->onDelete('cascade');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');
            $table->text('starter_code')->nullable();
            $table->json('test_cases'); // JSON array of test cases
            $table->json('hints')->nullable(); // JSON array of hints
            $table->integer('max_attempts')->default(10);
            $table->integer('time_limit')->default(30); // minutes
            $table->integer('points')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('challenges');
    }
};
