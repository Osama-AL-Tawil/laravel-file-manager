<?php
namespace OST\LaravelFileManager\database\migrations;

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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->unsignedTinyInteger('model_name')->index();
            $table->unsignedBigInteger('model_id');
            $table->string('file_name')->index();
            $table->string('file_path');
            $table->string('file_type')->nullable();
            $table->string('file_size');
            $table->string('thumbnail')->nullable();
            $table->string('disk');
            $table->string('bucket_name')->nullable();
            $table->integer('order');
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_blocked')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->on('users')->references('user_id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('message_files');
    }
};
