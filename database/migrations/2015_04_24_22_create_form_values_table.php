<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('form_values', function(Blueprint $table){
			$table->integer('form_object_id')->unsigned();
			$table->foreign('form_object_id')->references('id')->on('form_objects')->onDelete('cascade');
			$table->integer('form_property_id')->unsigned();
			$table->foreign('form_property_id')->references('id')->on('form_properties')->onDelete('cascade');
			$table->integer('submission_id')->unsigned();
			$table->foreign('submission_id')->references('id')->on('submissions')->onDelete('cascade');
			$table->text('value');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('form_values');
	}

}
