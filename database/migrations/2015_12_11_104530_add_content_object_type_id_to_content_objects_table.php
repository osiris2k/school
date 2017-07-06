<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentObjectTypeIdToContentObjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('content_objects', function (Blueprint $table) {
            if (!Schema::hasColumn('content_objects', 'content_object_types_id')) {
                $table->integer('content_object_types_id')->unsigned()->default(1)->after('id');
            }
            $table->foreign('content_object_types_id')->references('id')
                ->on('content_object_types')
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
        Schema::table('content_objects', function (Blueprint $table) {
            $table->dropForeign('content_objects_content_object_types_id_foreign');
            $table->dropColumn('content_object_types_id');
        });
    }

}
