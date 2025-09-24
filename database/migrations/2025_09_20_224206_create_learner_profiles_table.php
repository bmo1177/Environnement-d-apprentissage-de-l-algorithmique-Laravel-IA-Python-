<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLearnerProfilesTable extends Migration
{
    public function up()
    {
        Schema::create('learner_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Cognitive Profile
            $table->float('problem_solving_score')->default(0);
            $table->float('logical_reasoning_score')->default(0);
            $table->float('pattern_recognition_score')->default(0);
            $table->float('abstraction_score')->default(0);
            
            // Behavioral Profile
            $table->integer('total_attempts')->default(0);
            $table->integer('successful_attempts')->default(0);
            $table->float('average_time_per_challenge')->default(0);
            $table->integer('hints_used')->default(0);
            $table->json('preferred_challenge_types')->nullable();
            
            // Motivational Profile
            $table->float('engagement_level')->default(50);
            $table->float('persistence_score')->default(50);
            $table->integer('streak_days')->default(0);
            $table->date('last_active_date')->nullable();
            $table->json('achievements')->nullable();
            
            // Learning Style
            $table->enum('learning_style', ['visual', 'verbal', 'sequential', 'global'])->default('sequential');
            $table->enum('pace', ['slow', 'moderate', 'fast'])->default('moderate');
            
            // Performance Metrics
            $table->json('competency_scores')->nullable(); // JSON object with competency_id => score
            $table->float('overall_performance')->default(0);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('learner_profiles');
    }
};
