<?php

use App\Models\Account;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('call_reason_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Account::class);
            $table->string('label');
            $table->timestamps();
        });

        Schema::create('call_reasons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('call_reason_type_id');
            $table->string('label');
            $table->timestamps();
            $table->foreign('call_reason_type_id')->references('id')->on('call_reason_types')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('call_reason_types');
        Schema::dropIfExists('call_reasons');
    }
};
