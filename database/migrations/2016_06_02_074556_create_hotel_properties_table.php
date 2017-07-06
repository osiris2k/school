<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHotelPropertiesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_properties', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('data_type_id')->unsigned();
            $table->string('name', 50);
            $table->string('variable_name', 50)->unique();
            $table->boolean('is_mandatory')->default(0);
            $table->json('options');
            $table->tinyInteger('priority')->default(0);
            $table->tinyInteger('created_by')->default(0);
            $table->tinyInteger('updated_by')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('hotel_properties', function (Blueprint $table) {
            $table->foreign('data_type_id')
                ->references('id')->on('data_types')
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
        Schema::table('hotel_properties', function (Blueprint $table) {
            $table->dropForeign('hotel_properties_data_type_id_foreign');
        });

        Schema::drop('hotel_properties');
    }

}
