<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomizeComponentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customize_components', function(Blueprint $table){
            $table->increments('id');
            $table->integer('type_id')->unsigned()->nullable(); //if type id = null means support all type
            $table->integer('step_id')->unsigned(); // componets will display in which customize steps
            $table->string('value');
            $table->string('type'); //type to define the value in 3 format (plain text, image, color)
            $table->text('description')->nullable();
            $table->integer('layer')->nullable();
            $table->integer('level')->default(1);
            $table->string('level_title')->nullable();
            $table->integer('front_image')->unsigned()->nullable();
            $table->integer('back_image')->unsigned()->nullable();
            $table->string('personalize')->nullable();
            $table->tinyInteger('available')->default(1);
            $table->tinyInteger('blank')->nullable();
            $table->tinyInteger('size_component')->nullable();
            $table->text('size_image')->nullable();

            $table->foreign('type_id')
                  ->references('id')->on('customize_types')
                  ->onDelete('cascade');
            $table->foreign('step_id')
                  ->references('id')->on('customize_steps')
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
        Schema::drop('customize_components');
    }
}
