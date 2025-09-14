<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSurveyNumberToSurveysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('surveys', 'survey_number')) {
                $table->integer('survey_number')->nullable()->after('id');
            }
        });
        
        // Update existing surveys with sequential numbers if they don't have them
        $surveys = DB::table('surveys')->whereNull('survey_number')->orderBy('id')->get();
        foreach ($surveys as $index => $survey) {
            DB::table('surveys')
                ->where('id', $survey->id)
                ->update(['survey_number' => $index + 1]);
        }
        
        // Make the field not nullable after populating existing records
        Schema::table('surveys', function (Blueprint $table) {
            if (Schema::hasColumn('surveys', 'survey_number')) {
                $table->integer('survey_number')->nullable(false)->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn('survey_number');
        });
    }
}
