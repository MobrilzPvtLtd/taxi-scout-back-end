<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyNameToAdminDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('admin_details', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('service_location_id');
        });

        Schema::table('drivers', function (Blueprint $table) {
            $table->string('driving_license')->nullable()->after('service_location_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('admin_details', function (Blueprint $table) {
            $table->dropColumn('company_name');
        });
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('driving_license');
        });
    }
}
