<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeightAndIsCorrectToOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('options', function (Blueprint $table) {
            if (!Schema::hasColumn('options', 'weight')) {
                $table->float('weight')->nullable()->after('label');
            }
            if (!Schema::hasColumn('options', 'is_correct')) {
                $table->boolean('is_correct')->default(false)->after('weight');
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
        Schema::table('options', function (Blueprint $table) {
            $table->dropColumn(['weight', 'is_correct']);
        });
    }
}
