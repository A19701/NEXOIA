<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatbotController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->middleware('throttle:5,1')
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:5,1')
        ->name('register.store');
});

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [ChatbotController::class, 'index'])
        ->name('dashboard');

    Route::post('/chatbot/send', [ChatbotController::class, 'send'])
        ->middleware('throttle:10,1')
        ->name('chatbot.send');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');
});
Route::delete('/chatbot/conversaciones/{id}', function (
    \Illuminate\Http\Request $request, $id
) {
    $conversacion = $request->user()
        ->conversaciones()
        ->findOrFail($id);

    $conversacion->delete();

    return redirect()->route('dashboard');
})->middleware('auth')
  ->whereNumber('id')
  ->name('chatbot.destroy');
