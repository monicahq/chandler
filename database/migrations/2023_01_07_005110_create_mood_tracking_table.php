<?php

use App\Models\Vault;
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

        Vault::chunk(200, function ($vaults) {
            foreach ($vaults as $vault) {
                DB::table('mood_tracking_parameters')->insert([
                    'vault_id' => $vault->id,
                    'label' => trans('vault.settings_mood_tracking_parameters_awesome'),
                    'hex_color' => 'bg-lime-500',
                ]);

                DB::table('mood_tracking_parameters')->insert([
                    'vault_id' => $vault->id,
                    'label' => trans('vault.settings_mood_tracking_parameters_good'),
                    'hex_color' => 'bg-lime-300',
                ]);

                DB::table('mood_tracking_parameters')->insert([
                    'vault_id' => $vault->id,
                    'label' => trans('vault.settings_mood_tracking_parameters_meh'),
                    'hex_color' => 'bg-cyan-600',
                ]);

                DB::table('mood_tracking_parameters')->insert([
                    'vault_id' => $vault->id,
                    'label' => trans('vault.settings_mood_tracking_parameters_bad'),
                    'hex_color' => 'bg-orange-300',
                ]);

                DB::table('mood_tracking_parameters')->insert([
                    'vault_id' => $vault->id,
                    'label' => trans('vault.settings_mood_tracking_parameters_awful'),
                    'hex_color' => 'bg-red-700',
                ]);
            }
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
