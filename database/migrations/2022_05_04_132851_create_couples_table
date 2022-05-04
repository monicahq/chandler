<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // necessary for SQLlite
        Schema::enableForeignKeyConstraints();

        Schema::create('couples', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vault_id');
            $table->string('name')->nullable();
            $table->timestamps();
            $table->foreign('vault_id')->references('id')->on('vaults')->onDelete('cascade');
        });

        Schema::create('contact_couple', function (Blueprint $table) {
            $table->unsignedBigInteger('couple_id');
            $table->unsignedBigInteger('contact_id');
            $table->timestamps();
            $table->foreign('couple_id')->references('id')->on('couples')->onDelete('cascade');
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
        });

        Schema::table('contacts', function (Blueprint $table) {
            $table->boolean('is_in_a_couple')->after('can_be_deleted')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('couples');
        Schema::dropIfExists('contact_couple');
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('is_in_a_couple');
        });
    }
};
