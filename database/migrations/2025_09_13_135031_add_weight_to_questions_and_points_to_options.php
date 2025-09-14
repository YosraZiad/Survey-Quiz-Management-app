<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeightToQuestionsAndPointsToOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Weight already exists in questions table from previous migration
        // Schema::table('questions', function (Blueprint $table) {
        //     $table->float('weight')->nullable()->after('points'); // for surveys
        // });

        Schema::table('options', function (Blueprint $table) {
            $table->integer('points')->nullable()->after('weight'); // for quizzes
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::table('questions', function (Blueprint $table) {
        //     $table->dropColumn('weight');
        // });

        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn('points');
        });
    }
}
