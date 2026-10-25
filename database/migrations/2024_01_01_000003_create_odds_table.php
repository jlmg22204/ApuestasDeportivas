<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('odds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->string('type'); // home, draw, away
            $table->string('label'); // "Victoria Local", "Empate", "Victoria Visitante"
            $table->decimal('value', 5, 2); // e.g., 1.75, 3.20, 4.50
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('odds');
    }
};
