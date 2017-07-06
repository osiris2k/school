<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('menu_group_id')->unsigned();
			$table->foreign('menu_group_id')->references('id')->on('menu_groups')->onDelete('cascade');
			$table->integer('content_id')->unsigned();
			$table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
			$table->string('menu_title',255);
			$table->string('menu_title_friendly_url',255);
			$table->integer('parent_menu_id')->nullable();
			$table->integer('priority')->defualt(100);
			$table->text('external_link')->nullable();
			$table->string('target')->defualt('_self');
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
		Schema::drop('menus');
	}

}
