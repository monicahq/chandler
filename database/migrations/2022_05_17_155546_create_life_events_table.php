<?php

use App\Models\Contact;
use App\Models\Vault;
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
        Schema::dropIfExists('contact_activity_participants');
        Schema::dropIfExists('contact_life_event_activities');
        Schema::dropIfExists('activities');
        Schema::dropIfExists('activity_types');
        Schema::dropIfExists('contact_life_events');
        Schema::dropIfExists('life_event_types');
        Schema::dropIfExists('life_event_categories');

        Schema::create('life_event_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vault::class)->constrained()->cascadeOnDelete();
            $table->integer('position')->nullable();
            $table->string('label')->nullable();
            $table->string('label_translation_key')->nullable();
            $table->boolean('can_be_deleted')->default(false);
            $table->timestamps();
        });

        Schema::create('life_event_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('life_event_category_id');
            $table->string('label')->nullable();
            $table->string('label_translation_key')->nullable();
            $table->boolean('can_be_deleted')->default(false);
            $table->integer('position')->nullable();
            $table->timestamps();
            $table->foreign('life_event_category_id')->references('id')->on('life_event_categories')->onDelete('cascade');
        });

        Schema::create('timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Vault::class)->constrained()->cascadeOnDelete();
            $table->date('started_at');
            $table->string('label')->nullable();
            $table->boolean('collapsed')->default(true);
            $table->timestamps();
        });

        Schema::create('timeline_event_participants', function (Blueprint $table) {
            $table->foreignIdFor(Contact::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('timeline_event_id');
            $table->timestamps();
            $table->foreign('timeline_event_id')->references('id')->on('timeline_events')->onDelete('cascade');
        });

        Schema::create('life_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('timeline_event_id');
            $table->unsignedBigInteger('life_event_type_id');
            $table->unsignedBigInteger('emotion_id')->nullable();
            $table->date('happened_at');
            $table->boolean('collapsed')->default(false);
            $table->string('summary')->nullable();
            $table->text('description')->nullable();
            $table->integer('costs')->nullable();
            $table->unsignedBigInteger('currency_id')->nullable();
            $table->foreignIdFor(Contact::class, 'paid_by_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->integer('duration_in_minutes')->nullable();
            $table->integer('distance')->nullable();
            $table->char('distance_unit', 2)->nullable();
            $table->string('from_place')->nullable();
            $table->string('to_place')->nullable();
            $table->string('place')->nullable();
            $table->timestamps();
            $table->foreign('timeline_event_id')->references('id')->on('timeline_events')->onDelete('cascade');
            $table->foreign('life_event_type_id')->references('id')->on('life_event_types')->onDelete('cascade');
            $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
            $table->foreign('emotion_id')->references('id')->on('emotions')->onDelete('set null');
        });

        Schema::create('life_event_participants', function (Blueprint $table) {
            $table->foreignIdFor(Contact::class)->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('life_event_id');
            $table->timestamps();
            $table->foreign('life_event_id')->references('id')->on('life_events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('life_event_categories');
        Schema::dropIfExists('life_event_types');
        Schema::dropIfExists('life_events');
        Schema::dropIfExists('life_event_participants');
    }
};
