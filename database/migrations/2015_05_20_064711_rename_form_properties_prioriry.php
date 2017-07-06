<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameFormPropertiesPrioriry extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('form_properties', function($table)
		{
		    $table->renameColumn('prioriry', 'priority');
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
		    $table->renameColumn('priority', 'prioriry');
		});
	}

}
