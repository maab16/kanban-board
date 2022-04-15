<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KanbanBoardController;

Route::get('/', [KanbanBoardController::class , 'index']);

Auth::routes();

// Route::get('/home', [App\Http\Controllers\HomeController::class , 'index'])->name('home');
