<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCountryToOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->string('country')->nullable()->after('city');
            $table->string('state')->nullable()->after('country');
        });

        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->string('model_name')->nullable()->after('capacity');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn('country');
            $table->dropColumn('state');
        });

        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->dropColumn('model_name');
        });
    }
}
