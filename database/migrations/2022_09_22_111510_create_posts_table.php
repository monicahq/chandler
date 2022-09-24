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
        Schema::create('post_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('label');
            $table->integer('position');
            $table->timestamps();
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });

        Schema::create('post_type_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_type_id');
            $table->string('label');
            $table->integer('position');
            $table->timestamps();
            $table->foreign('post_type_id')->references('id')->on('post_types')->onDelete('cascade');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_id');
            $table->string('title');
            $table->text('content');
            $table->datetime('written_at');
            $table->timestamps();
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_types');
        Schema::dropIfExists('post_type_sections');
        Schema::dropIfExists('posts');
    }
};
