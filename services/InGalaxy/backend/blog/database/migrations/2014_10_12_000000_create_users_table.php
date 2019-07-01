<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->increments('id');
            $table->string('login')->unique();
            $table->string('password');
            $table->string("name")->nullable();
            $table->string("surname")->nullable();
            $table->string("race")->nullable();
            $table->string("gender")->nullable();
            $table->dateTime("birthday")->nullable();
            $table->string("homeplace")->nullable();
            $table->string("avatar")->nullable()->default("img/galaxy.jpg");
            $table->text("selfdescription")->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
