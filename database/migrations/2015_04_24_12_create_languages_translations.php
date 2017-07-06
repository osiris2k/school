<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesTranslations extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('language_translation',function(Blueprint $table){
			$table->increments('id');
			$table->integer('translation_id')->unsigned();
			$table->foreign('translation_id')->references('id')->on('translations')->onDelete('cascade');
			$table->integer('language_id')->unsigned();
			$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
			$table->text('values')->nullable();
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
		Schema::drop('language_translation');
	}

}
