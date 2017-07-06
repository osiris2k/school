<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteValuesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('site_values', function(Blueprint $table)
		{
			$table->integer('site_id')->unsigned();
			$table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			$table->integer('site_property_id')->unsigned();
			$table->foreign('site_property_id')->references('id')->on('site_properties')->onDelete('cascade');
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
		Schema::drop('site_values');
	}

}
