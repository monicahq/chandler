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
        Schema::table('accounts', function (Blueprint $table) {
            $table->string('licence_key', 4096)->after('uuid')->nullable();
            $table->datetime('valid_until_at')->after('licence_key')->nullable();
            $table->string('purchaser_email', 255)->after('valid_until_at')->nullable();
            $table->string('frequency', 15)->after('purchaser_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn('licence_key');
            $table->dropColumn('valid_until_at');
            $table->dropColumn('purchaser_email');
            $table->dropColumn('frequency');
        });
    }
};
