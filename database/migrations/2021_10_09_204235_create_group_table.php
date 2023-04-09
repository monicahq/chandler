<?php

use App\Models\Account;
use App\Models\Contact;
use App\Models\Vault;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('group_types', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Account::class)->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->integer('position');
            $table->timestamps();
        });

        Schema::create('group_type_roles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_type_id');
            $table->string('label');
            $table->integer('position')->nullable();
            $table->timestamps();
            $table->foreign('group_type_id')->references('id')->on('group_types')->onDelete('cascade');
        });

        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->foreignIdFor(Vault::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('group_type_id');
            $table->string('name');
            $table->timestamps();

            $table->foreign('group_type_id')->references('id')->on('group_types')->onDelete('cascade');

            if (config('scout.driver') === 'database' && in_array(DB::connection()->getDriverName(), ['mysql', 'pgsql'])) {
                $table->fullText('name');
            }
        });

        Schema::create('contact_group', function (Blueprint $table) {
            $table->unsignedBigInteger('group_id');
            $table->foreignIdFor(Contact::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('group_type_role_id')->nullable();
            $table->timestamps();
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('group_type_role_id')->references('id')->on('group_type_roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('group_types');
        Schema::dropIfExists('group_type_roles');
        Schema::dropIfExists('groups');
        Schema::dropIfExists('contact_group');
    }
};
