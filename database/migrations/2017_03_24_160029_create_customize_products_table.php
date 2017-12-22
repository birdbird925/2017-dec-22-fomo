<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomizeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customize_products', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('components');
            $table->text('image')->nullable();
            $table->text('images');
            $table->text('thumb');
            $table->text('back');
            $table->text('description');
            $table->integer('type_id')->unsigned();
            $table->decimal('price', 5, 2);
            $table->integer('created_by')->unsigned()->nullable();

            $table->foreign('type_id')
                  ->references('id')->on('customize_types')
                  ->onDelete('cascade');
            $table->foreign('created_by')
                  ->references('id')->on('users')
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
        Schema::drop('customize_products');
    }
}
