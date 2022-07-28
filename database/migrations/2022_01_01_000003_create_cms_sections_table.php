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
        Schema::create('cms_sections', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('cms_section_group_id')
                ->nullable()
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('set null');
            $table->string('name', 255);
            $table->string('folder', 255)->unique();
            $table->string('icon', 30)->nullable();
            $table->string('description', 512)->nullable();
            $table->boolean('is_published')->default(0)->index();
            $table
                ->integer('sort_order')
                ->default(0)
                ->nullable(false)
                ->unsigned();
            $table->index('cms_section_group_id');
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
        Schema::dropIfExists('cms_sections');
    }
};
