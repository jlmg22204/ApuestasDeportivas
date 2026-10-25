<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('odd_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('odd_value', 5, 2); // snapshot of odd at time of bet
            $table->decimal('potential_winnings', 10, 2);
            $table->string('status')->default('pending'); // pending, won, lost, cancelled
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bets');
    }
};
