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
        Schema::create('mood_tracking_parameters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vault_id');
            $table->string('label');
            $table->string('hex_color');
            $table->string('position')->nullable();
            $table->timestamps();
            $table->foreign('vault_id')->references('id')->on('vaults')->onDelete('cascade');
        });

        Schema::create('mood_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('mood_tracking_parameter_id');
            $table->datetime('rated_at');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->foreign('mood_tracking_parameter_id')->references('id')->on('mood_tracking_parameters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mood_tracking_parameters');
        Schema::dropIfExists('mood_tracking');
    }
};
