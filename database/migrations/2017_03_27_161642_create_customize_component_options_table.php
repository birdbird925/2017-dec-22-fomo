<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomizeComponentOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customize_component_options', function(Blueprint $table){
            $table->increments('id');
            $table->integer('type_id')->unsigned()->nullable();
            $table->integer('component_id')->unsigned();
            $table->string('value');
            $table->string('type'); //type to define the value in 3 format (plain text, image, color)
            $table->integer('layer')->nullable();
            $table->text('description')->nullable();
            $table->integer('front_image')->unsigned();
            $table->integer('back_image')->unsigned()->nullable();
            $table->tinyInteger('available')->default(1);
            $table->text('size_image')->nullable();

            $table->foreign('type_id')
                  ->references('id')->on('customize_types')
                  ->onDelete('cascade');
            $table->foreign('component_id')
                  ->references('id')->on('customize_components')
                  ->onDelete('cascade');
            $table->foreign('front_image')
                  ->references('id')->on('images')
                  ->onDelete('cascade');
            $table->foreign('back_image')
                  ->references('id')->on('images')
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
        Schema::drop('customize_component_options');
    }
}
