<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsContentsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hotels_contents', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('hotel_id')->unsigned();
			$table->integer('content_id')->unsigned();
			$table->timestamps();

			$table->foreign('hotel_id')
				->references('id')->on('hotels')
				->onUpdate('cascade')
				->onDelete('cascade');
			
			$table->foreign('content_id')
				->references('id')->on('contents')
				->onUpdate('cascade')
				->onDelete('cascade');

		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('hotels_contents', function (Blueprint $table) {
			$table->dropForeign('hotels_contents_hotel_id_foreign');
			$table->dropForeign('hotels_contents_content_id_foreign');
		});

		Schema::drop('hotels_contents');
	}

}
