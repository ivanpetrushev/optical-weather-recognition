<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
        });

        Schema::create('cameras', function (Blueprint $table) {
            $table->increments('id');
            $table->text('name');
            $table->unsignedInteger('location_id');

            $table->index('location_id');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
        });

        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->unsignedInteger('location_id');
            $table->unsignedInteger('camera_id');
            $table->text('dir');
            $table->string('filename', 255)->default('');
            $table->date('taken_date');
            $table->time('taken_time');
            $table->json('histogram_lightness')->nullable();
            $table->json('histogram_hue')->nullable();
            $table->json('histogram_saturation')->nullable();

            $table->index('location_id');
            $table->foreign('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->index('camera_id');
            $table->foreign('camera_id')->references('id')->on('cameras')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
