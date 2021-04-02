<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmployeeSidCompanySidTerritoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_to_companies', function (Blueprint $table) {
            $table->id();
            $table->string('employee_sid');
            $table->string('company_sid');
            $table->string('territory_sid');
            $table->boolean('is_active');
            $table->timestamps();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_to_companies');
    }
}
