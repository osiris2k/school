<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDataTypeOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('data_type_options', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('data_type_id')->unsigned();
			$table->foreign('data_type_id')->references('id')->on('data_types')->onDelete('cascade');
			$table->string('name',45);
			$table->string('type',45);
			$table->boolean('is_mandatory')->default(0);
			$table->string('default',45)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('data_type_options');
	}

}
