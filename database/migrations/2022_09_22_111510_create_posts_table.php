<?php

use App\Models\Account;
use App\Models\Contact;
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
        Schema::create('post_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Account::class)->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->integer('position');
            $table->boolean('can_be_deleted')->default(true);
            $table->timestamps();
        });

        Schema::create('post_template_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_template_id');
            $table->string('label');
            $table->integer('position');
            $table->boolean('can_be_deleted')->default(true);
            $table->timestamps();
            $table->foreign('post_template_id')->references('id')->on('post_templates')->onDelete('cascade');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('journal_id');
            $table->boolean('published')->default(false);
            $table->string('title')->nullable();
            $table->integer('view_count')->default(0);
            $table->datetime('written_at');
            $table->timestamps();
            $table->foreign('journal_id')->references('id')->on('journals')->onDelete('cascade');
        });

        Schema::create('post_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->integer('position');
            $table->string('label');
            $table->text('content')->nullable();
            $table->timestamps();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

        Schema::create('contact_post', function (Blueprint $table) {
            $table->unsignedBigInteger('post_id');
            $table->foreignIdFor(Contact::class)->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vault_id');
            $table->string('name');
            $table->string('slug');
            $table->timestamps();
            $table->foreign('vault_id')->references('id')->on('vaults')->onDelete('cascade');
        });

        Schema::create('post_tag', function (Blueprint $table) {
            $table->unsignedBigInteger('tag_id');
            $table->unsignedBigInteger('post_id');
            $table->timestamps();
            $table->foreign('tag_id')->references('id')->on('tags')->onDelete('cascade');
            $table->foreign('post_id')->references('id')->on('posts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_templates');
        Schema::dropIfExists('post_template_sections');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_sections');
        Schema::dropIfExists('contact_post');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('post_tag');
    }
};
