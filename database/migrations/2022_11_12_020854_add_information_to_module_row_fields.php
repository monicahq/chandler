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
        Schema::table('module_row_fields', function (Blueprint $table) {
            $table->string('help')->nullable()->after('label');
            $table->string('placeholder')->nullable()->after('help');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('module_row_fields', function (Blueprint $table) {
            $table->dropColumn('help');
            $table->dropColumn('placeholder');
        });
    }
};
