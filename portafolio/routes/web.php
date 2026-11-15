<?php

use App\Http\Controllers\Admin\ConversationController;
use App\Http\Controllers\ChatController;
use App\Http\Middleware\ChatbotAdminAuth;
use Illuminate\Support\Facades\Route;

Route::view('/', 'portafolio')->name('home');

// Asistente IA
Route::post('/chat/messages', [ChatController::class, 'store'])
    ->middleware('throttle:chatbot')
    ->name('chat.store');

// Panel de conversaciones y leads
Route::prefix('admin')->middleware(ChatbotAdminAuth::class)->name('admin.')->group(function () {
    Route::get('/conversaciones', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversaciones/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::patch('/leads/{lead}', [ConversationController::class, 'updateLead'])->name('leads.update');
});
