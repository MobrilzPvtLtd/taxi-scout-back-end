<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('driving_license')->nullable()->after('service_location_id');
            $table->boolean('smoking')->default(0)->nullable()->after('available');
            $table->boolean('pets')->default(0)->nullable()->after('smoking');
            $table->boolean('drinking')->default(0)->nullable()->after('pets');
            $table->boolean('handicaped')->default(0)->nullable()->after('drinking');
        });

        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->uuid('owner_id')->after('company_key')->nullable();

            $table->boolean('smoking')->default(0)->nullable()->after('active');
            $table->boolean('pets')->default(0)->nullable()->after('smoking');
            $table->boolean('drinking')->default(0)->nullable()->after('pets');
            $table->boolean('handicaped')->default(0)->nullable()->after('drinking');
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->uuid('owner_unique_id')->after('id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('driving_license');
            $table->dropColumn('smoking');
            $table->dropColumn('pets');
            $table->dropColumn('drinking');
            $table->dropColumn('handicaped');
        });

        Schema::table('vehicle_types', function (Blueprint $table) {
            $table->dropColumn('smoking');
            $table->dropColumn('pets');
            $table->dropColumn('drinking');
            $table->dropColumn('handicaped');
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn('owner_unique_id');
        });
    }
}
