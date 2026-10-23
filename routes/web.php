<?php

use App\Http\Controllers\DisplayStateController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DisplayStateController::class, 'editor'])->name('display.editor');
Route::get('/client', [DisplayStateController::class, 'client'])->name('display.client');
Route::get('/display-state', [DisplayStateController::class, 'show'])->name('display.state');
Route::put('/display-state', [DisplayStateController::class, 'update'])
    ->middleware('throttle:120,1')
    ->name('display.update');
