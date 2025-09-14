<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\WordImportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/surveys', [SurveyController::class, 'index']);
Route::post('/surveys', [SurveyController::class, 'store']);
Route::get('/surveys/{survey}', [SurveyController::class, 'show']);
Route::put('/surveys/{survey}', [SurveyController::class, 'update']);
Route::delete('/surveys/{survey}', [SurveyController::class, 'destroy']);
Route::post('/surveys/{survey}/publish', [SurveyController::class, 'publish']);

Route::post('/surveys/{survey}/responses', [ResponseController::class, 'store']);
Route::get('/surveys/{survey}/responses', [ResponseController::class, 'index']);
Route::get('/surveys/{survey}/analytics', [ResponseController::class, 'analytics']);

// Import questions from Word document
Route::post('/surveys/import/word', [WordImportController::class, 'import']);

// Account creation for successful users
Route::post('/surveys/{survey}/responses/{response}/account', [ResponseController::class, 'createAccount']);

// Response details
Route::get('/responses/{response}', [ResponseController::class, 'getResponseDetails']);

// Dashboard stats
Route::get('/dashboard/stats', [\App\Http\Controllers\DashboardController::class, 'getStats']);
