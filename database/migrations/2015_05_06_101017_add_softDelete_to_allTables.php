<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftDeleteToAllTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$tables = ['users',
					'sites',
					'site_properties',
					'site_values',
					'form_objects',
					'languages',
					'translations',
					'language_translation',
					'menu_groups',
					'contents',
					'menus',
					'content_objects',
					'content_properties',
					'content_values',
					'submissions',
					'form_properties',
					'form_property_labels',
					'form_values'];
		foreach ($tables as $table_name) {
			Schema::table($table_name, function(Blueprint $table){$table->softDeletes();});	
		}
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$tables = ['users',
					'sites',
					'site_properties',
					'site_values',
					'form_objects',
					'languages',
					'translations',
					'language_translation',
					'menu_groups',
					'contents',
					'menus',
					'content_objects',
					'content_properties',
					'content_values',
					'submissions',
					'form_properties',
					'form_property_labels',
					'form_values'];
		foreach ($tables as $table_name) {
			Schema::table($table_name, function(Blueprint $table){
				$table->dropSoftDeletes();
			});	
		}
	}

}
