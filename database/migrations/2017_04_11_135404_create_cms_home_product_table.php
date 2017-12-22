<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsHomeProductTable extends Migration
{
    public function up()
    {
        Schema::create('cms_product', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('background')->unsigned();

            $table->foreign('product_id')
                ->references('id')->on('customize_products')
                ->onDelete('cascade');
            $table->foreign('background')
                ->references('id')->on('images')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::drop('cms_product');
    }
}
