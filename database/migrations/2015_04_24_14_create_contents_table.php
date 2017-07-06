<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('contents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name',255);
			$table->integer('parent_content_id')->nullable();
			$table->integer('site_id')->unsigned();
			$table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			$table->boolean('allow_cross_site')->default(0); 
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
		Schema::drop('contents');
	}

}
