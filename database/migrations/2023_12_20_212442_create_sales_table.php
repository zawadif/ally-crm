<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('head_office');
            $table->unsignedBigInteger('head_office_unit');
            $table->foreign('head_office')->references('id')->on('offices');
            $table->foreign('head_office_unit')->references('id')->on('units');
            $table->string('job_category')->nullable();
            $table->string('job_title')->nullable();
            $table->string('postcode')->nullable();
            $table->string('job_type')->nullable();
            $table->string('time')->nullable();
            $table->string('salary')->nullable();
            $table->string('experience')->nullable();
            $table->string('qualification')->nullable();
            $table->longText('benefits')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
//            $table->enum('status',['active','pending','reject'])->default('pending');
            $table->string('status')->default('pending');
            $table->string('sale_added_time')->nullable();
            $table->string('sale_added_date')->nullable();
            $table->string('job_title_prof')->nullable();
            $table->string('send_cv_limit')->nullable();
            $table->longText('sale_notes')->nullable();
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
        Schema::dropIfExists('sales');
    }
}
