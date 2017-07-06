<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedirecturlsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('redirecturls', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('site_id')->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			$table->integer('type')->unsigned();
			$table->text('source_url');
			$table->text('destination_url'); 
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('redirecturls');
	}

}
