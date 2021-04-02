<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('district', function (Blueprint $table) {
            $table->id();
            $table->string('code',20);
            $table->string('name',100);
            $table->string('display_name',100);
            $table->string('prefix',100);
            $table->string('name_convert',100);
            $table->string('ref_code',20);
            $table->tinyInteger('is_active');
            $table->string('province_code',20);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('district');
    }
}
