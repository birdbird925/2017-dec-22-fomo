<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomizeTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customize_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 5, 2);
            $table->integer('front_image')->unsigned()->nullable();
            $table->integer('back_image')->unsigned()->nullable();
            $table->integer('front_personalize')->unsigned()->nullable();
            $table->integer('back_personalize')->unsigned()->nullable();
            $table->text('size_image')->nullable();

            $table->foreign('front_image')
                  ->references('id')->on('images')
                  ->onDelete('cascade');
            $table->foreign('back_image')
                  ->references('id')->on('images')
                  ->onDelete('cascade');
              $table->foreign('front_personalize')
                    ->references('id')->on('images')
                    ->onDelete('cascade');
              $table->foreign('back_personalize')
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
        Schema::drop('customize_types');
    }
}
