<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCreatedByAndUpdatedByNullable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		$tables = ['users',
					'language_translation',
					'translations',
					'media',
					'contents',
					'languages',
					'form_objects',
					'form_properties',
					'form_property_labels',
					'content_objects',
					'content_properties',
					'menu_groups',
					'menus',
					'sites',
					'site_properties'];
		foreach ($tables as $table_name) {
			Schema::table($table_name, function(Blueprint $table){
				$table->integer('created_by')->nullable()->unsigned()->default(null)->change();
				$table->integer('updated_by')->nullable()->unsigned()->default(null)->change();
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
		$tables = ['users',
					'language_translation',
					'translations',
					'media',
					'contents',
					'languages',
					'form_objects',
					'form_properties',
					'form_property_labels',
					'content_objects',
					'content_properties',
					'menu_groups',
					'menus',
					'sites',
					'site_properties'];
		foreach ($tables as $table_name) {
			Schema::table($table_name, function(Blueprint $table){
				$table->integer('created_by')->nullable(false)->change();
				$table->integer('updated_by')->nullable(false)->change();
			});	
		}
	}

}
