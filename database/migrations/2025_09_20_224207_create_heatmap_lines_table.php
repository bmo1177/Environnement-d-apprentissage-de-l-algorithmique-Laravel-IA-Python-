<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHeatmapLinesTable extends Migration
{
    public function up()
    {
        Schema::create('heatmap_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('challenge_id')->constrained()->onDelete('cascade');
            $table->foreignId('attempt_id')->constrained()->onDelete('cascade');
            $table->integer('line_number');
            $table->enum('status', ['correct', 'error', 'warning', 'info']);
            $table->text('message')->nullable();
            $table->integer('frequency')->default(1); // How many times this line had issues
            $table->timestamps();
            
            $table->unique(['attempt_id', 'line_number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('heatmap_lines');
    }
};
