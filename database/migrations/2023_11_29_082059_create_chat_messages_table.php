<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('chat_id');
            $table->integer('sender_id');
            $table->integer('receiver_id');
            $table->text('message')->nullable();
            $table->integer('image_status')->default(0);
            $table->json('image_url')->nullable();
            $table->integer('seen_count')->default(0);
            $table->timestamps();
            $table->foreign('chat_id')
                    ->references('id')
                    ->on('chat')
                    ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
