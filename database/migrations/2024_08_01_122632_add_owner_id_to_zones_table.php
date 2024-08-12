<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOwnerIdToZonesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->string('owner_id')->nullable()->after('company_key');
        });

        Schema::table('zone_type_price', function (Blueprint $table) {
            $table->string('owner_id')->nullable()->after('zone_type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zones', function (Blueprint $table) {
            $table->dropColumn('owner_id');
        });

        Schema::table('zone_type_price', function (Blueprint $table) {
            $table->dropColumn('owner_id');
        });
    }
}
