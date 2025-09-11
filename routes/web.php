<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('index');
});

Route::get('/responses', function () {
    return view('responses');
});
Route::get('/responses/{survey}', function (\App\Models\Survey $survey) {
    return view('response-detail', ['surveyId' => $survey->id]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/analytics', function () {
    return view('analytics');
});

// Preview page (builder preview)
Route::get('/preview/{survey}', function (\App\Models\Survey $survey) {
    return view('preview', ['surveyId' => $survey->id]);
});

// Public fill page (shareable link)
Route::get('/s/{survey}', function (\App\Models\Survey $survey) {
    abort_unless($survey->is_published, 404);
    return view('fill', ['surveyId' => $survey->id]);
});

// Surveys management
Route::get('/surveys', function () {
    return view('manage');
});
