<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMessageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_owner')->unsigned();
            $table->foreign('id_owner')->references('id')->on('users');
            $table->integer('id_dialogs')->unsigned();
            $table->foreign('id_dialogs')->references('id')->on('dialogs');
            $table->text("text");
            $table->string("image")->nullable();

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
        Schema::dropIfExists('message');
    }
}
