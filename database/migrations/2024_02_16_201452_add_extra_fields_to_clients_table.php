<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraFieldsToClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('is_in_crm_request')->default(false);
            $table->boolean('is_in_crm_reject')->default(false);
            $table->boolean('is_in_crm_request_reject')->default(false);
            $table->boolean('is_crm_request_confirm')->default(false);
            $table->boolean('is_crm_interview_attended')->default(false);
            $table->boolean('is_in_crm_start_date')->default(false);
            $table->boolean('is_in_crm_invoice')->default(false);
            $table->boolean('is_in_crm_start_date_hold')->default(false);
            $table->boolean('is_in_crm_paid')->default(false);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
