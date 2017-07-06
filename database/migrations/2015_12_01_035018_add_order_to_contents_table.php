<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderToContentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if (!Schema::hasColumn('contents', 'display_order'))
		{
			Schema::table('contents', function(Blueprint $table)
			{
				$table->integer('display_order')->default(0)->after('allow_cross_site');
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
		Schema::table('contents', function(Blueprint $table)
		{
			//
			$table->dropColumn('display_order');
		});
	}

}
