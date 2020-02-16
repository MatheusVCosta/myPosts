<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFriends extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('friends'))
        {
            $this->down();
        }
        Schema::create('friends', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('user_invited');
            $table->unsignedBigInteger('friend');
            $table->boolean('is_friend');
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
        Schema::dropIfExists('friends');
    }
}
