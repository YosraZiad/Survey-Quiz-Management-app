<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Option;
use App\Models\Question;
use App\Models\Respondent;
use App\Models\Response as SurveyResponse;
use App\Models\Survey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResponseController extends Controller
{
    public function index(Request $request, Survey $survey)
    {
        $query = SurveyResponse::where('survey_id', $survey->id)
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

        $total = SurveyResponse::where('survey_id',$survey->id)->count();
        $avgScore = round(SurveyResponse::where('survey_id',$survey->id)->avg('score') ?? 0, 2);
        $recent = SurveyResponse::where('survey_id',$survey->id)
            ->where('created_at','>=', now()->subDay())
            ->count();

        $totalQuestions = max(1, $survey->questions()->count());
        $allForCompletion = SurveyResponse::where('survey_id',$survey->id)
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
    public function analytics(Survey $survey)
    {
        $questions = $survey->questions()->with('options')->get();
        $result = [];
        foreach ($questions as $q) {
            $entry = [
                'question_id' => $q->id,
                'title' => $q->title,
                'type' => $q->type,
                'total_answers' => 0,
                'option_counts' => [],
                'average' => null,
                'top_values' => [],
            ];

            if (in_array($q->type, ['radio','checkbox','dropdown'])) {
                $optionIds = $q->options->pluck('id');
                $counts = Answer::whereIn('option_id', $optionIds)->select('option_id', DB::raw('count(*) as c'))
                    ->groupBy('option_id')->pluck('c','option_id');
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
            } elseif ($q->type === 'rating') {
                $vals = Answer::where('question_id', $q->id)->pluck('value')->filter()->map(fn($v)=> (float)$v);
                $entry['total_answers'] = $vals->count();
                $entry['average'] = $entry['total_answers'] > 0 ? round($vals->avg(), 2) : null;
            } elseif (in_array($q->type, ['number'])) {
                $vals = Answer::where('question_id', $q->id)->pluck('value')->filter()->map(fn($v)=> (float)$v);
                $entry['total_answers'] = $vals->count();
                $entry['average'] = $entry['total_answers'] > 0 ? round($vals->avg(), 2) : null;
            } else {
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
    public function store(Request $request, Survey $survey)
    {
        $data = $request->validate([
            'respondent' => 'required|array',
            'respondent.email' => 'required|email|ends_with:gmail.com',
            'respondent.name' => 'nullable|string',
            'answers' => 'required|array',
        ]);

        return DB::transaction(function () use ($survey, $data) {
            $respondent = null;
            if (!empty($data['respondent'])) {
                $respondent = Respondent::create($data['respondent']);
            }

            $response = SurveyResponse::create([
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
                            $score += (int)($question->points ?? 1);
                        }
                    } elseif (in_array($question->type, ['number','date','rating'])) {
                        // extend as needed for non-option quiz questions
                    }
                } else { // survey weighting
                    if ($optionId) {
                        $opt = Option::where('question_id', $question->id)->find($optionId);
                        $score += (float)($opt->weight ?? 0);
                    } elseif ($question->type === 'rating') {
                        $score += ((float)($question->metadata['weight_per_star'] ?? 1)) * ((float)$value);
                    }
                }
            }

            $response->update(['score' => $score]);
            return $response->load('answers');
        });
    }
}


