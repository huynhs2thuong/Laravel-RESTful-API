<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanActualTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_actual', function (Blueprint $table) {
            $table->id();
            $table->string('sid');
            $table->string('plan_sid');
            $table->string('status',50);
            $table->date('actual_date');
            $table->integer('is_manual');
            $table->decimal('lat',10,8);
            $table->decimal('lng',10,8);
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
        Schema::dropIfExists('plan_actual');
    }
}
