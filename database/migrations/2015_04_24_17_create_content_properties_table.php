<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentPropertiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('content_properties', function(Blueprint $table){
			$table->increments('id');
			$table->integer('content_object_id')->unsigned();
			$table->foreign('content_object_id')->references('id')->on('content_objects')->onDelete('cascade');
			$table->integer('data_type_id')->unsigned();
			$table->foreign('data_type_id')->references('id')->on('data_types')->onDelete('cascade');
			$table->string('name',45);
			$table->string('variable_name',45);
			$table->string('default_value',45);
			$table->boolean('is_mandatory')->default(0); 
			$table->text('options')->nullable();
			$table->integer('priority')->default(100);
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
		Schema::drop('content_properties');
	}

}
