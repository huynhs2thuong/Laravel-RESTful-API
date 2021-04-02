<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('sid');
            $table->string('territory_sid');
            $table->string('company_sid');
            $table->string('name');
            $table->string('phone');
            $table->string('climate_region_sid');
            $table->string('store_type_sid');
            $table->string('address_1');
            $table->string('address_2')->nullable();
            $table->string('city');
            $table->string('states')->nullable();
            $table->string('zip_code')->nullable();
            $table->boolean('is_active');
            $table->string('img_store')->nullable();
            $table->string('file_store')->nullable();
            $table->string('file_number')->nullable();
            $table->string('day_on_file')->nullable();
            $table->string('opening_hour')->nullable();
            $table->string('created_by',50)->nullable();
            $table->string('updated_by',50)->nullable();
            $table->string('deleted_by',50)->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at',6)->nullable();
            

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');

    }
}
