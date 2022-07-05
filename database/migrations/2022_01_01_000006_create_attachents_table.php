<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 512);
            $table->string('original_name', 512);
            $table->string('mime', 64);
            $table->string('extension', 10)->nullable();
            $table->bigInteger('size')->default(0);
            $table->integer('sort')->default(0);
            $table->string('path', 512);
            $table->string('description', 512)->nullable();
            $table->string('alt')->nullable();
            $table->string('hash')->nullable();
            $table->string('disk', 64)->default('public');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_type', 128)->nullable();
            $table->json('additional')->nullable();
            $table->string('group')->nullable();
            $table->timestamps();
        });

        Schema::create('attachmentable', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attachmentable_type');
            $table->unsignedInteger('attachmentable_id');
            $table->unsignedInteger('attachment_id');

            $table->index(['attachmentable_type', 'attachmentable_id']);

            $table->foreign('attachment_id')
                ->references('id')
                ->on('attachments')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('attachmentable');
        Schema::dropIfExists('attachments');
    }
};
