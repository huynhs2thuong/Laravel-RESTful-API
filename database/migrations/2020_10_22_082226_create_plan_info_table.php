<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_info', function (Blueprint $table) {
            $table->id();
            $table->string('sid');
            $table->string('status',20);
            $table->string('last_chek_in',32);
            $table->string('last_chek_out',32);
            $table->integer('is_manual');
            $table->string('plan_actual_sid',32);
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
        Schema::dropIfExists('plan_info');
    }
}
