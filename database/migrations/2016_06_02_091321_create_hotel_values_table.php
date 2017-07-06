<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelValuesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_values', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('hotel_id')->unsigned();
            $table->integer('hotel_property_id')->unsigned();
            $table->integer('language_id')->unsigned();
            $table->json('value');
            $table->timestamps();
            $table->softDeletes();

        });

        Schema::table('hotel_values', function (Blueprint $table) {
            $table->foreign('hotel_id')
                ->references('id')->on('hotels')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('hotel_property_id')
                ->references('id')->on('hotel_properties')
                ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('language_id')
                ->references('id')->on('languages')
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
        Schema::table('hotel_values', function (Blueprint $table) {
            $table->dropForeign('hotel_values_hotel_id_foreign');
            $table->dropForeign('hotel_values_hotel_property_id_foreign');
        });

        Schema::drop('hotel_values');
    }

}
