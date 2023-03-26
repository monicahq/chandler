<?php

use App\Models\Account;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contact_information_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Account::class);
            $table->string('name');
            $table->string('protocol')->nullable();
            $table->boolean('can_be_deleted')->default(true);
            $table->string('type')->nullable();
            $table->timestamps();
        });

        Schema::create('contact_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contact_id');
            $table->unsignedBigInteger('type_id');
            $table->string('data');
            $table->timestamps();
            $table->foreign('contact_id')->references('id')->on('contacts')->onDelete('cascade');
            $table->foreign('type_id')->references('id')->on('contact_information_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('contact_information_types');
        Schema::dropIfExists('contact_information');
    }
};
