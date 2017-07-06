<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteLanguagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_languages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('site_id')->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			$table->integer('language_id')->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
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
		Schema::drop('site_languages');
	}

}
