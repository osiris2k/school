<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTokenToUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{

		if (Schema::hasColumn('users', 'token'))
		{
			Schema::table('users', function(Blueprint $table)
			{
				//add Token
				$table->string('token',100)->nullable();
			});
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasColumn('users', 'token'))
		{
			Schema::table('users', function(Blueprint $table)
			{
				$table->dropColumn('token');
			});
		}
		
	}

}
