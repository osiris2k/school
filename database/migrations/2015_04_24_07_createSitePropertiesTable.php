<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitePropertiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_properties', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('data_type_id')->unsigned();
			$table->foreign('data_type_id')->references('id')->on('data_types')->onDelete('cascade');
			// $table->integer('site_id')->unsigned();
			// $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			$table->string('name',45);
			$table->string('variable_name',45);
			$table->boolean('is_mandatory')->default(0); 
			$table->text('options');
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
		Schema::drop('site_properties');
	}

}
