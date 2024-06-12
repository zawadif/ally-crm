<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('name');
            $table->string('postcode');
            $table->string('type');
            $table->string('contact_number');
            $table->string('contact_landline')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->double('lat', 10, 6)->nullable();
            $table->double('long', 10, 6)->nullable();
            $table->string('office_added_time')->nullable();
            $table->string('office_added_date')->nullable();
            $table->longText('office_notes')->nullable();
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
        Schema::dropIfExists('offices');
    }
}
