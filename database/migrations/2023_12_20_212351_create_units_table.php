<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('head_office');
            $table->foreign('head_office')->references('id')->on('offices');
            $table->string('unit_name')->nullable();
            $table->string('unit_postcode')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone_number')->nullable();
            $table->string('contact_landline')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('website')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('units');
    }
}
