<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMenusTableAddImageSrcField extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up() {
		Schema::table( 'menus', function ( Blueprint $table ) {
			$table->string( 'image_src' )->after( 'parent_menu_id' );
		} );
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down() {
		Schema::table( 'menus', function ( Blueprint $table ) {
			$table->dropColumn( [ 'image_src' ] );
		} );
	}

}
