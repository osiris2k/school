<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelsFormObjectsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotels_form_objects', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hotel_id')->unsigned();
            $table->integer('form_object_id')->unsigned();
            $table->timestamps();

            $table->foreign('hotel_id')
                ->references('id')->on('hotels')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('form_object_id')
                ->references('id')->on('form_objects')
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
        Schema::table('hotels_form_objects', function (Blueprint $table) {
            $table->dropForeign('hotels_form_objects_hotel_id_foreign');
            $table->dropForeign('hotels_form_objects_form_object_id_foreign');
        });

        Schema::drop('hotels_form_objects');
    }

}
