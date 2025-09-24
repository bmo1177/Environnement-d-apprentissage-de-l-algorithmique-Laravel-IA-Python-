<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetenciesTable extends Migration
{
    public function up()
    {
        Schema::create('competencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('domain'); // e.g., 'algorithms', 'data_structures', 'problem_solving'
            $table->integer('level')->default(1);
            $table->integer('max_score')->default(100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('competencies');
    }
};
