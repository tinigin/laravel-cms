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
        if (!Schema::hasTable('cms_notifications')) {
            Schema::create('cms_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('subject');
                $table->text('message');
                $table->boolean('is_important')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('cms_user_notifications')) {
            Schema::create('cms_user_notifications', function (Blueprint $table) {
                $table->id();
                $table
                    ->foreignId('from_user_id')
                    ->nullable()
                    ->constrained('cms_users')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table
                    ->foreignId('cms_user_id')
                    ->constrained()
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table
                    ->foreignId('cms_notification_id')
                    ->constrained()
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
                $table->timestamp('readed_at')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cms_user_notifications');
        Schema::dropIfExists('cms_notifications');
    }
};
