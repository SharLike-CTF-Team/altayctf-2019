<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFriendsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::enableForeignKeyConstraints();
        Schema::create('friends', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_owner')->unsigned();
            $table->foreign('id_owner')->references('id')->on('users');
            $table->integer('id_subject')->unsigned();
            $table->foreign('id_subject')->references('id')->on('users');
            $table->boolean('approve');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friends');
    }
}
