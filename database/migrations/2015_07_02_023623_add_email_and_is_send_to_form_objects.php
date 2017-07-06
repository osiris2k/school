<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailAndIsSendToFormObjects extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('form_objects', function(Blueprint $table){
			$table->string('email')->after('name');
			$table->integer('is_send')->after('email');			
		});	
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('form_objects', function(Blueprint $table){
			$table->dropColumn('email');
			$table->dropColumn('is_send');
		});	
	}

}
