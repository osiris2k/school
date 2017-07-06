<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddVariableNameToFormProperties extends Migration {

	public function up()
	{
		Schema::table('form_properties', function($table)
		{
		    $table->string('variable_name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('form_properties', function($table)
		{
			$table->dropColumn('variable_name');
		});
	}

}
