<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJsonStructureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('json_structures', function (Blueprint $table) {
            $table->id();
            $table->string('created_by',50)->nullable();
            $table->string('updated_by',50)->nullable();
            $table->string('deleted_by',50)->nullable();
            $table->dateTime('created_at',6)->nullable();
            $table->dateTime('updated_at',6)->nullable();
            $table->dateTime('deleted_at',6)->nullable();
            $table->string('code',100);
            $table->longText('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('json_structures');
    }
}