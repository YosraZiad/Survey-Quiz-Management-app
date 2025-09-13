<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WordImportController extends Controller
{
    // Minimal DOCX parser: extracts paragraphs as lines; supports simple Q/A pattern
    public function import(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|file|mimes:docx,txt',
                'type' => 'nullable|in:survey,quiz'
            ]);

            $type = $request->input('type', 'survey');
            $path = $request->file('file')->getRealPath();

            // تحقق من وجود مكتبة ZipArchive
            if (!class_exists('ZipArchive')) {
                return response()->json(['error' => 'ZipArchive PHP extension is not installed.'], 500);
            }

            $text = '';
            if ($request->file('file')->getClientOriginalExtension() === 'docx') {
                $text = $this->extractTextFromDocx($path);
                if (empty($text)) {
                    return response()->json(['error' => 'Failed to extract text from DOCX. Please check the file format.'], 500);
                }
            } else {
                $text = file_get_contents($path);
                if ($text === false) {
                    return response()->json(['error' => 'Failed to read text file.'], 500);
                }
            }

            // Normalize heavy punctuation and bullets
            $normalized = $this->normalizeText($text);
            $lines = collect(preg_split('/\r?\n/', $normalized))
                ->map(fn($line) => trim($line))
                ->filter(fn($line) => !empty($line))
                ->values();

            $questions = [];
            $currentQuestion = null;
            $currentOptions = [];

            foreach ($lines as $line) {
                if (preg_match('/^\s*(\d{1,3})\s*[)\.:\-]+\s*(.+)$/u', $line, $matches) ||
                    preg_match('/^\s*(سؤال\s*\d+)\s*[:\-\.)]?\s*(.+)$/u', $line, $matches)) {
                    // Save previous question
                    if ($currentQuestion) {
                        $questions[] = $this->makeQuestionFromLines($currentQuestion, $currentOptions, $type);
                    }
                    // Start new question
                    $currentQuestion = trim($matches[2]);
                    $currentOptions = [];
                } elseif (preg_match('/^\s*[a-zA-Z]\s*[)\.:\-]+\s*(.+)$/u', $line, $matches) ||
                         preg_match('/^\s*\p{Arabic}\s*[)\.:\-]+\s*(.+)$/u', $line, $matches) ||
                         preg_match('/^\s*[\-\*\•]\s*(.+)$/u', $line, $matches)) {
                    // This is an option
                    if ($currentQuestion) {
                        $currentOptions[] = trim($matches[1]);
                    }
                } else {
                    // This might be a continuation of the question or a standalone question
                    if (!$currentQuestion) {
                        $currentQuestion = $line;
                        $currentOptions = [];
                    }
                }
            }

            // Don't forget the last question
            if ($currentQuestion) {
                $questions[] = $this->makeQuestionFromLines($currentQuestion, $currentOptions, $type);
            }

            return response()->json(['questions' => $questions]);
        } catch (\Exception $e) {
            \Log::error('Word import error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Import failed',
                'details' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function makeQuestionFromLines(string $title, array $options, string $type)
    {
        $options = array_values(array_filter($options));
        $correctIndex = null;

        // Infer type
        $lower = mb_strtolower($title, 'UTF-8');
        $isMulti = preg_match('/(اختر\s*اكثر|يمكن\s*اختيار|multiple|checkbox)/u', $lower);
        $isRating = preg_match('/(تقييم|نجوم|rate|rating)/iu', $lower);
        $isDate = preg_match('/(تاريخ|date)/iu', $lower);
        $isNumber = preg_match('/(رقم|عمر|كم|number)/iu', $lower);
        
        // Check for dropdown-appropriate questions
        $isDropdown = $this->shouldBeDropdown($title, $options);

        if (!empty($options)) {
            $questionType = 'radio'; // default
            
            if ($isMulti) {
                $questionType = 'checkbox';
            } elseif ($isDropdown) {
                $questionType = 'dropdown';
            } elseif (count($options) > 5) {
                // If more than 5 options, use dropdown for better UX
                $questionType = 'dropdown';
            }
            
            return [
                'title' => $title,
                'type' => $questionType,
                'options' => $options,
                'correctAnswer' => $type === 'quiz' ? $correctIndex : null,
            ];
        }
        if ($isRating) {
            return [ 'title' => $title, 'type' => 'rating' ];
        }
        if ($isDate) {
            return [ 'title' => $title, 'type' => 'date' ];
        }
        if ($isNumber) {
            return [ 'title' => $title, 'type' => 'number' ];
        }
        
        // Check if this should be a predefined dropdown question
        $predefinedOptions = $this->getPredefinedOptions($title);
        if (!empty($predefinedOptions)) {
            return [
                'title' => $title,
                'type' => 'dropdown',
                'options' => $predefinedOptions,
                'correctAnswer' => null,
            ];
        }
        
        $isLong = mb_strlen($title, 'UTF-8') > 120; // heuristic
        return [ 'title' => $title, 'type' => $isLong ? 'long' : 'short' ];
    }

    private function extractTextFromDocx(string $path): string
    {
        // DOCX is a zip file; read word/document.xml
        $zip = new \ZipArchive();
        if ($zip->open($path) === true) {
            $index = $zip->locateName('word/document.xml');
            if ($index !== false) {
                $xml = $zip->getFromIndex($index);
                $zip->close();
                // remove tags and decode entities
                // Convert paragraph ends to newlines to keep structure
                $xml = preg_replace('/<\/w:p>/', "\n", $xml);
                $xml = preg_replace('/<\/?w:[^>]+>/', '', $xml);
                $xml = strip_tags($xml);
                $xml = html_entity_decode($xml, ENT_QUOTES | ENT_XML1, 'UTF-8');
                return $xml;
            }
            $zip->close();
        }
        return '';
    }

    private function normalizeText(string $text): string
    {
        $t = str_replace(["\r\n", "\r"], "\n", $text);
        // Replace common decorative asterisks with spaces
        $t = preg_replace('/\*{2,}/', ' ', $t);
        // Ensure each numbered question starts on its own line
        $t = preg_replace('/\s+(\d{1,3})\s*[\)\.:\-]+\s*/u', "\n$1. ", $t);
        // Ensure the word "سؤال" starts new block
        $t = preg_replace('/\s*(سؤال\s*\d+)\s*[:\-\.)]?\s*/u', "\n$1. ", $t);
        // Collapse duplicate newlines
        $t = preg_replace('/\n{2,}/', "\n", $t);
        return trim($t);
    }

    /**
     * Determine if a question should be a dropdown based on title and options
     */
    private function shouldBeDropdown(string $title, array $options): bool
    {
        $lower = mb_strtolower($title, 'UTF-8');
        
        // Geographic/location questions - force dropdown
        $locationKeywords = [
            'country', 'دولة', 'بلد', 'الدولة', 'البلد', 'اختر الدولة',
            'city', 'مدينة', 'المدينة',
            'state', 'ولاية', 'محافظة', 'الولاية', 'المحافظة',
            'region', 'منطقة', 'المنطقة',
            'nationality', 'جنسية', 'الجنسية'
        ];
        
        // Educational questions
        $educationKeywords = [
            'education', 'تعليم', 'التعليم', 'مؤهل', 'المؤهل',
            'degree', 'درجة', 'الدرجة', 'شهادة', 'الشهادة',
            'university', 'جامعة', 'الجامعة',
            'school', 'مدرسة', 'المدرسة'
        ];
        
        // Professional questions
        $professionKeywords = [
            'job', 'وظيفة', 'الوظيفة', 'عمل', 'العمل',
            'profession', 'مهنة', 'المهنة',
            'occupation', 'حرفة', 'الحرفة',
            'career', 'مسار', 'المسار'
        ];
        
        // Income/salary questions
        $incomeKeywords = [
            'income', 'دخل', 'الدخل', 'راتب', 'الراتب',
            'salary', 'أجر', 'الأجر', 'مرتب', 'المرتب'
        ];
        
        // Age range questions
        $ageKeywords = [
            'age group', 'فئة عمرية', 'الفئة العمرية',
            'age range', 'مدى عمري', 'المدى العمري'
        ];
        
        // Combine all keywords
        $allKeywords = array_merge(
            $locationKeywords,
            $educationKeywords, 
            $professionKeywords,
            $incomeKeywords,
            $ageKeywords
        );
        
        // Check if title contains any dropdown keywords
        foreach ($allKeywords as $keyword) {
            if (strpos($lower, $keyword) !== false) {
                return true;
            }
        }
        
        // Check if options suggest a dropdown (common dropdown patterns)
        if (!empty($options)) {
            $optionsText = mb_strtolower(implode(' ', $options), 'UTF-8');
            
            // Geographic patterns
            if (preg_match('/(مصر|السعودية|الإمارات|الكويت|قطر|البحرين|عمان|الأردن|فلسطين|لبنان|سوريا|العراق)/u', $optionsText)) {
                return true;
            }
            
            // Education level patterns
            if (preg_match('/(ثانوية|بكالوريوس|ماجستير|دكتوراه|دبلوم)/u', $optionsText)) {
                return true;
            }
            
            // Age range patterns
            if (preg_match('/(\d+\s*-\s*\d+|أقل من|أكثر من|من.*إلى)/u', $optionsText)) {
                return true;
            }
            
            // Income range patterns
            if (preg_match('/(ريال|دولار|جنيه|درهم|\$|€|£)/u', $optionsText)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get predefined options for common question types
     */
    private function getPredefinedOptions(string $title): array
    {
        $lower = mb_strtolower($title, 'UTF-8');
        
        // Gender questions
        if (preg_match('/(gender|جنس|الجنس|نوع)/u', $lower)) {
            return ['ذكر', 'أنثى'];
        }
        
        // Marital status
        if (preg_match('/(marital|زواج|الزواج|حالة اجتماعية|الحالة الاجتماعية)/u', $lower)) {
            return ['أعزب', 'متزوج', 'مطلق', 'أرمل'];
        }
        
        // Country questions (if title suggests it but no options provided)
        if (preg_match('/(country|دولة|بلد|الدولة|البلد)/u', $lower)) {
            return [
                'مصر', 'السعودية', 'الإمارات', 'الكويت', 'قطر', 
                'البحرين', 'عمان', 'الأردن', 'فلسطين', 'لبنان', 
                'سوريا', 'العراق', 'المغرب', 'الجزائر', 'تونس', 'ليبيا'
            ];
        }
        
        // Education level
        if (preg_match('/(education|تعليم|التعليم|مؤهل|المؤهل|شهادة|الشهادة)/u', $lower)) {
            return [
                'أقل من الثانوية', 'ثانوية عامة', 'دبلوم', 
                'بكالوريوس', 'ماجستير', 'دكتوراه'
            ];
        }
        
        // Age groups
        if (preg_match('/(age group|فئة عمرية|الفئة العمرية|عمر|العمر)/u', $lower)) {
            return [
                'أقل من 18', '18-25', '26-35', '36-45', 
                '46-55', '56-65', 'أكثر من 65'
            ];
        }
        
        // Income ranges
        if (preg_match('/(income|دخل|الدخل|راتب|الراتب)/u', $lower)) {
            return [
                'أقل من 3000', '3000-5000', '5001-8000', 
                '8001-12000', '12001-20000', 'أكثر من 20000'
            ];
        }
        
        // Employment status
        if (preg_match('/(employment|وظيفة|الوظيفة|عمل|العمل|توظيف|التوظيف)/u', $lower)) {
            return [
                'موظف حكومي', 'موظف قطاع خاص', 'أعمال حرة', 
                'طالب', 'متقاعد', 'عاطل عن العمل'
            ];
        }
        
        // Yes/No questions
        if (preg_match('/(هل|do you|are you|have you)/u', $lower)) {
            return ['نعم', 'لا'];
        }
        
        // Satisfaction levels
        if (preg_match('/(satisfaction|رضا|الرضا|راض|راضي)/u', $lower)) {
            return [
                'راض جداً', 'راض', 'محايد', 'غير راض', 'غير راض إطلاقاً'
            ];
        }
        
        // Frequency questions
        if (preg_match('/(frequency|تكرار|التكرار|كم مرة|كم مره)/u', $lower)) {
            return [
                'يومياً', 'أسبوعياً', 'شهرياً', 'نادراً', 'أبداً'
            ];
        }
        
        return [];
    }
}
