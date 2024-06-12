<?php

use App\Enums\GenderEnum;
use App\Enums\RelationEnum;
use App\Enums\UserStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('fullName')->nullable();
            $table->string('email')->unique();
            $table->string('uid')->nullable();
            $table->string('phoneNumber',100)->unique()->nullable();
            $table->integer('isVerified')->nullable();
            $table->enum('status', [UserStatusEnum::ACTIVE, UserStatusEnum::BLOCK, UserStatusEnum::REGISTRATION_INPROCESS])->default(UserStatusEnum::ACTIVE);
            $table->string('is_admin')->default(0);
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->unsignedInteger('createdAt')->nullable();
            $table->unsignedInteger('updatedAt')->nullable();
            $table->unsignedInteger('deletedAt')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Schema::dropIfExists('users');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
