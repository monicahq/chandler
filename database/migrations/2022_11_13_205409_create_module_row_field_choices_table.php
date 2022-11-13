<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('module_row_field_choices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('module_row_field_id');
            $table->string('label');
            $table->timestamps();
            $table->foreign('module_row_field_id')->references('id')->on('module_row_fields')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('module_row_field_choices');
    }
};
