<?php

use App\Http\Controllers\ClController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


    Route::get('/',[ClController::class,'index'])->name('index');
    Route::post('/play-matches', [ClController::class, 'playMatches'])->name('play-matches');
    Route::post('/play-all-remaining-games/{group}', [ClController::class, 'playAllRemainingGames'])->name('play-all-remaining-games');
    Route::post('/reset', [ClController::class, 'reset'])->name('reset');
    Route::get('/get-matches-by-week', 'ClController@getMatchesByWeek')->name('get.matches.by.week');



