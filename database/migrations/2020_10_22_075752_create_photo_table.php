<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photo_job', function (Blueprint $table) {
            $table->id();
            $table->string('sid');
            $table->string('job_sid');
            $table->string('store_sid');
            $table->string('territory_sid');
            $table->string('elevation_code',50);
            $table->string('original_file_name',150);
            $table->string('name');
            $table->string('description');
            $table->string('file');
            $table->tinyInteger('is_active');
            $table->char('plan_actual_sid');
            $table->string('created_by',50)->nullable();
            $table->string('updated_by',50)->nullable();
            $table->string('deleted_by',50)->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
            $table->tinyInteger('is_deleted')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('photo');
    }
}
