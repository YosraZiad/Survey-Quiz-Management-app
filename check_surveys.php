<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$surveys = App\Models\Survey::all();
echo "عدد الاستطلاعات: " . $surveys->count() . "\n";

foreach ($surveys as $survey) {
    echo "ID: {$survey->id}, Title: {$survey->title}, Published: " . ($survey->is_published ? 'نعم' : 'لا') . "\n";
}