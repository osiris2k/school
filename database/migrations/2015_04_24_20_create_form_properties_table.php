<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormPropertiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('form_properties',function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('form_object_id')->unsigned();
			$table->foreign('form_object_id')->references('id')->on('form_objects')->onDelete('cascade');
			$table->integer('data_type_id')->unsigned();
			$table->foreign('data_type_id')->references('id')->on('data_types')->onDelete('cascade');
			$table->string('name',45);
			$table->string('defualt_value',45);
			$table->boolean('is_mandatory')->default(0); 
			$table->text('options');
			$table->integer('prioriry')->default(100);
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
		Schema::drop('form_properties');
	}

}
