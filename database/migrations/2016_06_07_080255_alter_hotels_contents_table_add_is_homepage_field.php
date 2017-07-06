<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterHotelsContentsTableAddIsHomepageField extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotels_contents', function (Blueprint $table) {
            $table->tinyInteger('is_homepage_content')->default(0)->after('content_id');
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
            $table->dropColumn('is_homepage_content');
        });
    }

}
