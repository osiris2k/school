<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content_values', function(Blueprint $table){
			$table->integer('content_id')->unsigned();
			$table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
			$table->integer('content_property_id')->unsigned();
			$table->foreign('content_property_id')->references('id')->on('content_properties')->onDelete('cascade');
			$table->integer('language_id')->unsigned();
			$table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');
			$table->text('value');
			$table->integer('content_object_id')->unsigned();
			$table->foreign('content_object_id')->references('id')->on('content_objects')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('content_values');
	}

}
