<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSongsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('songs', static function (Blueprint $table): void {
            $table->increments('id');
            $table->integer('album_id')->unsigned()->nullable();
            $table->integer('artist_id')->unsigned()->nullable();
            $table->integer('year_id')->unsigned()->nullable();
            $table->integer('cover_id')->unsigned()->nullable();
            $table->string('title')->nullable();
            $table->float('length')->nullable();
            $table->string('path')->nullable();
            $table->string('file_name')->nullable();
            $table->integer('play_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('songs');
    }
}
