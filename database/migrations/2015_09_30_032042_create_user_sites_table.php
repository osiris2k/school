<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserSitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_sites', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('site_id')->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			$table->integer('user_id')->foreign('user_id')->references('id')->on('user')->onDelete('cascade');
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
		Schema::drop('user_sites');
	}

}
