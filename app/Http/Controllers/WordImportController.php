<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WordImportController extends Controller
{
    // Minimal DOCX parser: extracts paragraphs as lines; supports simple Q/A pattern
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:docx,txt',
            'type' => 'nullable|in:survey,quiz'
        ]);

        $type = $request->input('type', 'survey');
        $path = $request->file('file')->getRealPath();

        $text = '';
        if ($request->file('file')->getClientOriginalExtension() === 'docx') {
            $text = $this->extractTextFromDocx($path);
        } else {
            $text = file_get_contents($path);
        }

        // Normalize heavy punctuation and bullets
        $normalized = $this->normalizeText($text);
        $lines = collect(preg_split('/\r?\n/', $normalized))
            ->map(fn($l)=> trim($l))
            ->filter();

        // Split into question blocks by numbering or the word "سؤال"
        $questions = [];
        $current = [];
        foreach ($lines as $line) {
            if (preg_match('/^(?:سؤال\s*\d+\s*[:\-\.)]|Q\d+\.|Question\s*\d+\.|\d+\s*[:\-\.)])\s*/iu', $line)) {
                if (!empty($current)) {
                    $questions[] = $this->makeQuestionFromLines($current, $type);
                    $current = [];
                }
                $current[] = $line;
            } else {
                $current[] = $line;
            }
        }
        if (!empty($current)) {
            $questions[] = $this->makeQuestionFromLines($current, $type);
        }

        return response()->json([
            'questions' => array_values(array_filter($questions))
        ]);
    }

    private function makeQuestionFromLines(array $lines, string $type)
    {
        $firstLine = $lines[0] ?? 'Question';
        $title = trim(preg_replace('/^(?:Q\d*\.|سؤال\s*\d*\.|Question\s*\d*\.|\d+\s*[:\-\.)])/iu', '', $firstLine));

        $options = [];
        $correctIndex = null;

        // Collect option-like lines
        for ($i = 1; $i < count($lines); $i++) {
            $l = trim($lines[$i]);
            if (preg_match('/^(?:A\)|B\)|C\)|D\)|\-|\•|\*|\d+\)|\d+\.)\s*(.+)$/u', $l, $m)) {
                $opt = trim($m[1]);
                if ($opt !== '') {
                    $options[] = $opt;
                    if (stripos($l, '(correct)') !== false || stripos($l, '(صح)') !== false) {
                        $correctIndex = count($options) - 1;
                    }
                }
            }
        }

        // Infer type
        $lower = mb_strtolower($title, 'UTF-8');
        $isMulti = preg_match('/(اختر\s*اكثر|يمكن\s*اختيار|multiple|checkbox)/u', $lower);
        $isRating = preg_match('/(تقييم|نجوم|rate|rating)/iu', $lower);
        $isDate = preg_match('/(تاريخ|date)/iu', $lower);
        $isNumber = preg_match('/(رقم|عمر|كم|number)/iu', $lower);

        if (!empty($options)) {
            return [
                'title' => $title,
                'type' => $isMulti ? 'checkbox' : 'radio',
                'options' => array_values($options),
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
}


