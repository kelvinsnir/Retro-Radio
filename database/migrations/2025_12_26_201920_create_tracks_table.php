<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tracks', function (Blueprint $table) {
            $table->id();
            $table->string('jamendo_id')->unique();
            $table->string('title');
            $table->string('artist');
            $table->string('album')->nullable();
            $table->integer('duration');
            $table->string('audio_url');
            $table->string('image_url')->nullable();
            $table->string('genre')->nullable();
            $table->integer('play_count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracks');
    }
};
