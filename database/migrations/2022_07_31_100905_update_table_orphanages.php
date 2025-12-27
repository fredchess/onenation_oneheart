<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableOrphanages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orphanages', function (Blueprint $table) {
            $table->json('data_identity');
            $table->json('data_identity_promoter')->nullable();
            $table->json('data_address')->nullable();
            $table->json('data_financial_infos')->nullable();
            $table->json('data_stats')->nullable();
            $table->json('data_education')->nullable();
            $table->json('data_needs')->nullable();
            $table->json('data_projects')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orphanages', function (Blueprint $table) {
            //
        });
    }
}
