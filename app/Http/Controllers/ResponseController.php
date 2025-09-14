<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\Option;
use App\Models\Response;
use App\Models\Respondent;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    public function index(Request $request, Survey $survey)
    {
        $query = Response::where('survey_id', $survey->id)
            ->with(['respondent','answers.question','answers.option'])
            ->latest();

        if ($request->query('export') === 'csv') {
            $rows = $query->get();
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="responses.csv"',
            ];
            $callback = function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Response ID','Respondent','Email','Score','Created At']);
                foreach ($rows as $r) {
                    $name = $r->respondent->name ?? '';
                    $email = $r->respondent->email ?? '';
                    fputcsv($out, [$r->id, $name, $email, $r->score, $r->created_at]);
                }
                fclose($out);
            };
            return response()->stream($callback, 200, $headers);
        }

        $data = $query->paginate(20);

        $total = Response::where('survey_id',$survey->id)->count();
        $avgScore = round(Response::where('survey_id',$survey->id)->avg('score') ?? 0, 2);
        $recent = Response::where('survey_id',$survey->id)
            ->where('created_at','>=', now()->subDay())
            ->count();

        $totalQuestions = max(1, $survey->questions()->count());
        $allForCompletion = Response::where('survey_id',$survey->id)
            ->with('answers')
            ->get();
        $completionRate = 0;
        if ($allForCompletion->count() > 0) {
            $sum = 0;
            foreach ($allForCompletion as $r) {
                $distinctAnswered = $r->answers->pluck('question_id')->unique()->count();
                $sum += min(1, $distinctAnswered / $totalQuestions);
            }
            $completionRate = round(($sum / $allForCompletion->count()) * 100, 2);
        }

        $stats = [
            'total' => $total,
            'avg_score' => $avgScore,
            'recent_24h' => $recent,
            'completion_rate' => $completionRate,
        ];
        return response()->json(['data' => $data, 'stats' => $stats]);
    }
    public function analytics($surveyId)
    {
        $survey = Survey::findOrFail($surveyId);
        $responses = Response::where('survey_id', $surveyId)
            ->with(['respondent', 'answers.option'])
            ->get();

        $analytics = [
            'total_responses' => $responses->count(),
            'completion_rate' => 100, // Assuming all loaded responses are complete
            'questions_analytics' => []
        ];

        $questions = Question::where('survey_id', $surveyId)->with('options')->get();
        $result = [];

        foreach ($questions as $q) {
            $entry = [
                'question_id' => $q->id,
                'title' => $q->title,
                'type' => $q->type,
                'option_counts' => []
            ];

            if (in_array($q->type, ['radio', 'checkbox', 'dropdown'])) {
                $counts = Answer::where('question_id', $q->id)
                    ->whereNotNull('option_id')
                    ->select('option_id', DB::raw('count(*) as count'))
                    ->groupBy('option_id')
                    ->pluck('count', 'option_id')
                    ->toArray();

                $sum = 0;
                foreach ($q->options as $opt) {
                    $count = (int)($counts[$opt->id] ?? 0);
                    $entry['option_counts'][] = [
                        'option_id' => $opt->id,
                        'label' => $opt->label,
                        'count' => $count,
                    ];
                    $sum += $count;
                }
                $entry['total_answers'] = $sum;
            } 
            
            if ($q->type === 'rating') {
                $vals = Answer::where('question_id', $q->id)->pluck('value')->filter()->map(fn($v)=> (float)$v);
                $entry['total_answers'] = $vals->count();
                $entry['average'] = $entry['total_answers'] > 0 ? round($vals->avg(), 2) : null;
            } 
            
            if (in_array($q->type, ['number'])) {
                $vals = Answer::where('question_id', $q->id)->pluck('value')->filter()->map(fn($v)=> (float)$v);
                $entry['total_answers'] = $vals->count();
                $entry['average'] = $entry['total_answers'] > 0 ? round($vals->avg(), 2) : null;
            } 
            
            if (!in_array($q->type, ['radio', 'checkbox', 'dropdown', 'rating', 'number'])) {
                $entry['total_answers'] = Answer::where('question_id', $q->id)->count();
                // For text-like questions, show top common answers
                if (in_array($q->type, ['short','long'])) {
                    $top = Answer::where('question_id', $q->id)
                        ->select('value', DB::raw('count(*) as c'))
                        ->whereNotNull('value')
                        ->groupBy('value')
                        ->orderByDesc('c')
                        ->limit(5)->get();
                    $entry['top_values'] = $top->map(fn($r)=> ['value'=>$r->value, 'count'=>(int)$r->c])->toArray();
                }
            }

            $result[] = $entry;
        }

        return response()->json(['data' => $result]);
    }

    public function show($responseId)
    {
        $response = Response::with(['respondent', 'answers.option', 'survey'])
            ->findOrFail($responseId);

        return response()->json([
            'success' => true,
            'data' => $response
        ]);
    }

    public function store(Request $request, Survey $survey)
    {
        // Check if survey is active before allowing responses
        if (!$survey->is_active) {
            return response()->json(['error' => 'Survey is currently closed'], 403);
        }

        $data = $request->validate([
            'respondent' => 'required|array',
            'respondent.email' => 'required|email',
            'respondent.name' => 'nullable|string',
            'answers' => 'required|array',
        ]);

        return DB::transaction(function () use ($survey, $data) {
            $respondent = null;
            if (!empty($data['respondent'])) {
                $respondent = Respondent::create($data['respondent']);
            }

            $response = Response::create([
                'survey_id' => $survey->id,
                'respondent_id' => $respondent->id ?? null,
            ]);

            $score = 0;
            foreach ($data['answers'] as $a) {
                $question = Question::where('survey_id', $survey->id)->findOrFail($a['question_id']);

                $optionId = $a['option_id'] ?? null;
                $value = $a['value'] ?? null;

                Answer::create([
                    'response_id' => $response->id,
                    'question_id' => $question->id,
                    'option_id' => $optionId,
                    'value' => $value,
                ]);

                // scoring
                if ($survey->type === 'quiz') {
                    if ($optionId) {
                        $opt = Option::where('question_id', $question->id)->find($optionId);
                        if ($opt && $opt->is_correct) {
                            // Use option points if available, otherwise use question points
                            $score += (int)($opt->points ?? $question->points ?? 1);
                        }
                    } elseif (in_array($question->type, ['short', 'long', 'number','date','rating'])) {
                        // For text questions, user gets full points if answered
                        if (!empty($value) && trim($value) !== '') {
                            $score += (int)($question->points ?? 1);
                        }
                    }
                } else { // survey weighting
                    if ($optionId) {
                        $opt = Option::where('question_id', $question->id)->find($optionId);
                        $score += (float)($opt->weight ?? 0);
                    } elseif (in_array($question->type, ['short', 'long', 'number', 'date'])) {
                        // For text questions, user gets full weight if answered
                        if (!empty($value) && trim($value) !== '') {
                            $score += (float)($question->weight ?? 1);
                        }
                    } elseif ($question->type === 'rating') {
                        $score += ((float)($question->weight ?? 1)) * ((float)$value);
                    }
                }
            }

            $response->update(['score' => $score]);
            return $response->load('answers');
        });
    }

    public function getResponseDetails($responseId)
    {
        try {
            // Check if response exists
            if (!Response::where('id', $responseId)->exists()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Response not found'
                ], 404);
            }

            // Get basic response data
            $response = Response::find($responseId);
            
            // Get survey data
            $survey = null;
            if ($response->survey_id) {
                $survey = Survey::with(['questions' => function($query) {
                    $query->with('options')->orderBy('display_order');
                }])->find($response->survey_id);
            }

            // Get respondent data
            $respondent = null;
            if ($response->respondent_id) {
                $respondent = Respondent::find($response->respondent_id);
            }

            // Get answers with their relationships
            $answers = Answer::where('response_id', $responseId)
                ->with(['question', 'option'])
                ->get();

            // Build response data manually
            $responseData = [
                'id' => $response->id,
                'survey_id' => $response->survey_id,
                'respondent_id' => $response->respondent_id,
                'score' => $response->score,
                'created_at' => $response->created_at,
                'updated_at' => $response->updated_at,
                'survey' => $survey,
                'respondent' => $respondent,
                'answers' => $answers
            ];

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in getResponseDetails for response ' . $responseId . ': ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}


