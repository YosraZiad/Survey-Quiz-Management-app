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

// All routes are now public (no authentication required)
Route::get('/', function () {
    $surveyId = request('survey');
    return view('index', ['surveyId' => $surveyId]);
});

Route::get('/edit/{survey}', function (\App\Models\Survey $survey) {
    return view('index', ['surveyId' => $survey->id, 'survey' => $survey]);
});

Route::get('/responses', function () {
    $surveyId = request('survey');
    if (!$surveyId) {
        return redirect('/surveys')->with('error', 'Please select a survey to view responses.');
    }
    return view('responses', ['surveyId' => $surveyId]);
});

Route::get('/response-detail/{surveyId}', function ($surveyId) {
    return view('response-detail', compact('surveyId'));
});

Route::get('/results', function () {
    return view('results');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index']);

Route::get('/analytics', function () {
    return view('analytics');
});

// Preview page (builder preview)
Route::get('/preview', function () {
    $surveyId = request('survey');
    return view('preview', ['surveyId' => $surveyId]);
});

Route::get('/preview/{survey}', function (\App\Models\Survey $survey) {
    return view('preview', ['surveyId' => $survey->id]);
});

// Public fill page (shareable link)
Route::get('/s/{survey}', function (\App\Models\Survey $survey) {
    abort_unless($survey->is_published && $survey->is_active, 404);
    return view('fill', ['surveyId' => $survey->id]);
});

// Surveys management
Route::get('/surveys', function () {
    return view('manage');
});
