<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSiteIdToFormObjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('form_objects', 'site_id')) {
            Schema::table('form_objects', function (Blueprint $table) {
                $table->integer('site_id')->unsigned()->after('slug');
                $table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
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
        Schema::table('form_objects', function (Blueprint $table) {
            $table->dropForeign('form_objects_site_id_foreign');
        });
    }

}
