<?php

use App\Http\Controllers\ArcController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ArcController::class, 'index'])->name('index');
Route::get('/arc/{id}', [ArcController::class, 'show'])->name('arc');
