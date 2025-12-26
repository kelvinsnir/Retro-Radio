<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MusicController;

Route::get('/', [MusicController::class, 'index'])->name('home');
Route::get('/search', [MusicController::class, 'search'])->name('music.search');
Route::get('/genre/{genre}', [MusicController::class, 'genre'])->name('music.genre');
Route::post('/track/save', [MusicController::class, 'saveTrack'])->name('track.save');
Route::post('/track/play/{id}', [MusicController::class, 'play'])->name('track.play');
