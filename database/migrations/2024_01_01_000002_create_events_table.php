<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained()->onDelete('cascade');
            $table->string('home_team');
            $table->string('away_team');
            $table->string('league')->nullable();
            $table->datetime('event_date');
            $table->string('status')->default('upcoming'); // upcoming, live, finished, cancelled
            $table->integer('home_score')->nullable();
            $table->integer('away_score')->nullable();
            $table->string('result')->nullable(); // home, draw, away
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
