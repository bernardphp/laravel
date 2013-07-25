<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBernardMessagesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bernard_messages', function(Blueprint $table) {
            $table->increments('id');
            $table->string('queue', 50);
            $table->boolean('visible');
            $table->dateTime('send_at');
            $table->text('message');
            $table->index(array('queue', 'visible', 'send_at'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bernard_messages');
    }

}
