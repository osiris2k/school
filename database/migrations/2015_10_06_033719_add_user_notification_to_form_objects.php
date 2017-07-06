<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserNotificationToFormObjects extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('form_objects', function(Blueprint $table)
		{
			$table->boolean('user_notification')->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('form_objects', function(Blueprint $table)
		{
			$table->dropColumn('user_notification');
		});
	}

}
