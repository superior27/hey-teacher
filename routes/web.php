<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if(app()->isLocal()) {
        auth()->loginUsingId(1);

        return to_route('dashboard');
    }

    return view('welcome');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::controller(QuestionController::class)
    ->middleware('auth')
    ->prefix('/questions')
    ->group(function () {
        Route::post('/', 'store')->name('questions.store');
        Route::put('/publish/{question_uuid}', 'publish')->name('questions.publish');
        Route::get('/', 'index')->name('questions.index');
        Route::delete('/{question_uuid}', 'destroy')->name('questions.destroy');
        Route::get('/{question_uuid}/edit', 'edit')->name('questions.edit');
        Route::put('/{question_uuid}', 'update')->name('questions.update');
    });

Route::controller(VoteController::class)
    ->prefix('/votes')
    ->group(function () {
        Route::post('/like/{question_uuid}', 'like')->name('votes.like');
        Route::post('/dislike/{question_uuid}', 'dislike')->name('votes.dislike');
    });

require __DIR__ . '/auth.php';
