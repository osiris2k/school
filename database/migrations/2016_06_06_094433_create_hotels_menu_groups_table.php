<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsMenuGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('hotels_menu_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('hotel_id')->unsigned();
			$table->integer('menu_group_id')->unsigned();
			$table->timestamps();

			$table->foreign('hotel_id')
				->references('id')->on('hotels')
				->onUpdate('cascade')
				->onDelete('cascade');

			$table->foreign('menu_group_id')
				->references('id')->on('menu_groups')
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
		Schema::table('hotels_menu_groups', function (Blueprint $table) {
			$table->dropForeign('hotels_menu_groups_hotel_id_foreign');
			$table->dropForeign('hotels_menu_groups_menu_group_id_foreign');
		});

		Schema::drop('hotels_menu_groups');
	}

}
