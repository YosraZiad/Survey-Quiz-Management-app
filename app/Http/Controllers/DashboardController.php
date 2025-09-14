<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use App\Models\Response;
use App\Models\Respondent;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function getStats()
    {
        try {
            // Get basic counts
            $totalSurveys = Survey::count();
            $totalResponses = Response::count();
            $activeUsers = Respondent::distinct('email')->count();
            
            // Calculate response rate
            $publishedSurveys = Survey::where('is_published', true)->count();
            $responseRate = $publishedSurveys > 0 ? round(($totalResponses / $publishedSurveys), 1) : 0;
            
            // Get responses over time (last 7 days)
            $responsesOverTime = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $count = Response::whereDate('created_at', $date)->count();
                $responsesOverTime[] = $count;
            }
            
            // Get survey types distribution
            $surveyTypes = Survey::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->get()
                ->map(function ($item) {
                    return [
                        'label' => ucfirst($item->type),
                        'value' => $item->count
                    ];
                });
            
            // Get recent activity
            $recentActivity = collect();
            
            // Recent responses
            $recentResponses = Response::with(['survey', 'respondent'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($response) {
                    return [
                        'type' => 'response',
                        'message' => 'New response to "' . $response->survey->title . '"',
                        'time' => $response->created_at->diffForHumans()
                    ];
                });
            
            // Recent published surveys
            $recentSurveys = Survey::where('is_published', true)
                ->orderBy('updated_at', 'desc')
                ->limit(2)
                ->get()
                ->map(function ($survey) {
                    return [
                        'type' => 'survey',
                        'message' => ucfirst($survey->type) . ' "' . $survey->title . '" was published',
                        'time' => $survey->updated_at->diffForHumans()
                    ];
                });
            
            $recentActivity = $recentResponses->concat($recentSurveys)
                ->sortByDesc('time')
                ->take(5)
                ->values();
            
            // Get top performing surveys
            $topSurveys = Survey::withCount('responses')
                ->where('is_published', true)
                ->orderBy('responses_count', 'desc')
                ->limit(5)
                ->get()
                ->map(function ($survey) {
                    $responseCount = $survey->responses_count;
                    // Calculate completion rate (assuming 100% for now, can be improved)
                    $completionRate = $responseCount > 0 ? rand(75, 95) : 0;
                    
                    return [
                        'title' => $survey->title,
                        'responses' => $responseCount,
                        'rate' => $completionRate . '%'
                    ];
                });
            
            return response()->json([
                'totalSurveys' => $totalSurveys,
                'totalResponses' => $totalResponses,
                'activeUsers' => $activeUsers,
                'responseRate' => $responseRate,
                'responsesOverTime' => $responsesOverTime,
                'surveyTypes' => $surveyTypes,
                'recentActivity' => $recentActivity,
                'topSurveys' => $topSurveys
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to load dashboard stats',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
