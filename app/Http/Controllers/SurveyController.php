<?php

namespace App\Http\Controllers;

use App\Models\Survey;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SurveyController extends Controller
{
    public function index()
    {
        return Survey::with('questions.options')->latest()->paginate(20);
    }

    public function show(Survey $survey)
    {
        return $survey->load('questions.options');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'type' => 'required|in:survey,quiz',
            'is_published' => 'boolean',
            'questions' => 'array'
        ]);

        return DB::transaction(function () use ($data) {
            $survey = Survey::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'type' => $data['type'],
                'is_published' => $data['is_published'] ?? false,
            ]);

            $this->syncQuestions($survey, $data['questions'] ?? []);
            return $survey->load('questions.options');
        });
    }

    public function update(Request $request, Survey $survey)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'type' => 'sometimes|in:survey,quiz',
            'is_published' => 'boolean',
            'questions' => 'array'
        ]);

        return DB::transaction(function () use ($survey, $data) {
            $survey->update($data);
            if (array_key_exists('questions', $data)) {
                $this->syncQuestions($survey, $data['questions']);
            }
            return $survey->load('questions.options');
        });
    }

    public function destroy(Survey $survey)
    {
        $survey->delete();
        return response()->noContent();
    }

    public function publish(Survey $survey)
    {
        $survey->update(['is_published' => true]);
        return $survey;
    }

    private function syncQuestions(Survey $survey, array $questions)
    {
        // Delete removed questions
        $keepIds = collect($questions)->pluck('id')->filter()->values();
        $survey->questions()->whereNotIn('id', $keepIds)->delete();

        foreach ($questions as $index => $qData) {
            $question = isset($qData['id'])
                ? Question::where('survey_id', $survey->id)->findOrFail($qData['id'])
                : new Question(['survey_id' => $survey->id]);

            $question->fill([
                'title' => $qData['title'] ?? 'Question',
                'type' => $qData['type'] ?? 'short',
                'required' => (bool)($qData['required'] ?? false),
                'points' => $qData['points'] ?? null,
                'display_order' => $index,
                'metadata' => $qData['metadata'] ?? null,
            ])->save();

            // sync options if provided
            if (in_array($question->type, ['radio','checkbox','dropdown'])) {
                $opts = $qData['options'] ?? [];
                $optKeepIds = collect($opts)->pluck('id')->filter();
                $question->options()->whereNotIn('id', $optKeepIds)->delete();
                foreach ($opts as $oi => $o) {
                    $option = isset($o['id'])
                        ? Option::where('question_id', $question->id)->findOrFail($o['id'])
                        : new Option(['question_id' => $question->id]);
                    $option->fill([
                        'label' => $o['label'] ?? 'Option',
                        'weight' => $o['weight'] ?? null,
                        'is_correct' => (bool)($o['is_correct'] ?? false),
                        'display_order' => $oi,
                    ])->save();
                }
            } else {
                // clear options for non-option questions
                $question->options()->delete();
            }
        }
    }
}


