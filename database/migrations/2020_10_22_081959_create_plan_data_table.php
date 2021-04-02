<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_data', function (Blueprint $table) {
            $table->id();
            $table->string('sid');
            $table->longText('data');
            $table->char('pg_id',32);
            $table->char('plan_actual_sid');
            $table->string('created_by',50)->nullable();
            $table->string('updated_by',50)->nullable();
            $table->string('deleted_by',50)->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plan_data');
    }
}
