<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomizeStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customize_steps', function(Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('extral_title')->nullable();
            $table->tinyInteger('primary');
            $table->string('direction')->default('front');
            $table->integer('type_id')->unsigned()->nullable();

            $table->foreign('type_id')
                  ->references('id')->on('customize_types')
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
        Schema::drop('customize_steps');
    }
}
