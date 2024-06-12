<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicantsPivotSalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applicants_pivot_sales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('sale_id');
            $table->string('is_interested')->nullable();
            $table->string('details')->nullable();
            $table->string('status')->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->foreign('sale_id')->references('id')->on('sales');
            $table->date('interest_added_date');
            $table->time('interest_added_time');
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
        Schema::dropIfExists('applicants_pivot_sales');
    }
}
