<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCmsPageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cms_page', function (Blueprint $table) {
          $table->increments('id');
          $table->string('page_title');
          $table->text('page_content');
          $table->string('page_content_size')->default('small');
          $table->string('page_slug')->unique();
          // seo optiomize
          $table->string('seo_title')->nullable();
          $table->text('seo_description')->nullable();
          $table->text('seo_keyword')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cms_page');
    }
}
