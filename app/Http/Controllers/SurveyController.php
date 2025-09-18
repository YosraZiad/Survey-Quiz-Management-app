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
        $surveys = Survey::with('questions.options')->get();
        return response()->json(['data' => $surveys]);
    }

    public function show(Survey $survey)
    {
        return response()->json($survey->load('questions.options'));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Survey store request received', ['data' => $request->all()]);
            
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
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

                if (isset($data['questions']) && !empty($data['questions'])) {
                    $this->syncQuestions($survey, $data['questions']);
                }
                
                return response()->json($survey->load('questions.options'));
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Survey validation error', ['errors' => $e->errors()]);
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Survey creation error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['error' => 'Failed to create survey: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Survey $survey)
    {
        try {
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => 'required|in:survey,quiz',
                'is_published' => 'boolean',
                'questions' => 'array'
            ]);

            return DB::transaction(function () use ($survey, $data) {
                $survey->update([
                    'title' => $data['title'],
                    'description' => $data['description'] ?? null,
                    'type' => $data['type'],
                    'is_published' => $data['is_published'] ?? false,
                ]);

                $this->syncQuestions($survey, $data['questions'] ?? []);
                return response()->json($survey->load('questions.options'));
            });
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Survey update error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update survey: ' . $e->getMessage()], 500);
        }
    }

    public function destroy(Survey $survey)
    {
        try {
            $survey->delete();
            return response()->json(['message' => 'Survey deleted successfully']);
        } catch (\Exception $e) {
            \Log::error('Survey deletion error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete survey: ' . $e->getMessage()], 500);
        }
    }

    public function publish(Survey $survey)
    {
        try {
            $survey->update(['is_published' => true]);
            return response()->json(['message' => 'Survey published successfully', 'survey' => $survey]);
        } catch (\Exception $e) {
            \Log::error('Survey publish error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to publish survey: ' . $e->getMessage()], 500);
        }
    }

    public function toggleActive(Request $request, Survey $survey)
    {
        try {
            $isActive = $request->input('is_active', true);
            $survey->update(['is_active' => $isActive]);
            
            $status = $isActive ? 'activated' : 'deactivated';
            return response()->json([
                'message' => "Survey {$status} successfully", 
                'survey' => $survey->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Survey toggle active error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update survey status: ' . $e->getMessage()], 500);
        }
    }

    private function syncQuestions(Survey $survey, array $questions)
    {
        try {
            \Log::info('Syncing questions', ['survey_id' => $survey->id, 'questions_count' => count($questions)]);
            
            // Delete removed questions
            $keepIds = collect($questions)->pluck('id')->filter()->values();
            $survey->questions()->whereNotIn('id', $keepIds)->delete();

            foreach ($questions as $index => $qData) {
                \Log::info('Processing question', ['index' => $index, 'data' => $qData]);
                
                $question = isset($qData['id'])
                    ? Question::where('survey_id', $survey->id)->findOrFail($qData['id'])
                    : new Question(['survey_id' => $survey->id]);

                $questionData = [
                    'title' => $qData['title'] ?? 'Question',
                    'type' => $qData['type'] ?? 'short',
                    'required' => (bool)($qData['required'] ?? false),
                    'display_order' => $index,
                    'metadata' => $qData['metadata'] ?? null,
                ];
                
                // Add points or weight based on survey type
                if ($survey->type === 'quiz' && isset($qData['points'])) {
                    $questionData['points'] = (int)$qData['points'];
                } elseif ($survey->type === 'survey' && isset($qData['weight'])) {
                    $questionData['weight'] = (float)$qData['weight'];
                }
                
                $question->fill($questionData)->save();

                // sync options if provided
                if (in_array($question->type, ['radio','checkbox','dropdown'])) {
                    $opts = $qData['options'] ?? [];
                    \Log::info('Processing options', ['question_id' => $question->id, 'options' => $opts]);
                    
                    $optKeepIds = collect($opts)->pluck('id')->filter();
                    $question->options()->whereNotIn('id', $optKeepIds)->delete();
                    
                    foreach ($opts as $oi => $o) {
                        // Skip completely empty options
                        if (is_null($o) || $o === '') {
                            \Log::warning('Skipping empty option', ['index' => $oi, 'option' => $o]);
                            continue;
                        }
                        
                        $option = isset($o['id'])
                            ? Option::where('question_id', $question->id)->findOrFail($o['id'])
                            : new Option(['question_id' => $question->id]);
                            
                        // Handle both string options and array options
                        $label = '';
                        if (is_string($o)) {
                            $label = trim($o);
                        } elseif (is_array($o)) {
                            if (isset($o['label'])) {
                                $label = trim($o['label']);
                            } elseif (isset($o[0])) {
                                $label = trim($o[0]);
                            }
                        }
                        // Ensure we always have a non-empty label
                        if (empty($label)) {
                            $label = 'Option ' . ($oi + 1);
                        }

                        $optionData = [
                            'label' => $label,
                            'weight' => null,
                            'points' => null,
                            'is_correct' => false,
                            'display_order' => $oi,
                        ];

                        // Set weight, points and is_correct if option is array
                        if (is_array($o)) {
                            if (isset($o['weight'])) {
                                $optionData['weight'] = (float)$o['weight'];
                            }
                            if (isset($o['points'])) {
                                $optionData['points'] = (int)$o['points'];
                            }
                            if (isset($o['is_correct'])) {
                                $optionData['is_correct'] = (bool)$o['is_correct'];
                            }
                        }

                        \Log::info('Saving option', ['option_data' => $optionData]);
                        $option->fill($optionData)->save();
                    }
                } else {
                    // clear options for non-option questions
                    $question->options()->delete();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error syncing questions: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }
}
