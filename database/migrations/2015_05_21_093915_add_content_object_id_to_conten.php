<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentObjectIdToConten extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('contents', function($table)
		{
		    $table->integer('content_object_id')->unsigned();
			$table->foreign('content_object_id')->references('id')->on('content_objects')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('contents', function($table)
		{
			$table->dropForeign('contents_content_object_id_foreign');
			$table->dropColumn('content_object_id');
		});
	}

}
