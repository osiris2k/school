<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AllTablesDefaultValue extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sites', function(Blueprint $table){
			$table->string('template')->nullable()->default(null)->change();
			$table->integer('active')->default(1)->change();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sites', function(Blueprint $table){
			$table->string('template')->nullable(false)->change();
			$table->integer('active')->nullable(false)->change();
		});
	}

}
