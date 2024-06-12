<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('app_name')->nullable();
            $table->string('app_email')->nullable();
            $table->string('app_phone')->nullable();
            $table->string('app_phoneHome')->nullable();
            $table->string('app_job_title')->nullable();
            $table->string('app_job_category')->nullable();
            $table->string('app_source')->nullable();
            $table->string('app_status')->nullable();
            $table->string('app_postcode')->nullable();
            $table->string('app_lat')->nullable();
            $table->string('app_long')->nullable();
            $table->string('applicant_added_time')->nullable();
            $table->string('applicant_cv')->nullable();
            $table->string('applicant_update_cv')->nullable();
            $table->string('app_job_title_prof')->nullable();
            $table->string('applicant_added_date')->nullable();
            $table->boolean('is_no_job')->default(0)->nullable();
            $table->boolean('is_blocked')->default(0)->nullable();
            $table->boolean('temp_not_interested')->default(0)->nullable();
            $table->boolean('is_paid')->default(0)->nullable();
            $table->boolean('is_nurse_home')->default(0)->nullable();
            $table->longText('applicant_notes')->nullable();
            $table->string('no_response')->nullable();
            $table->longText('applicant_notes')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clients');
    }
}
