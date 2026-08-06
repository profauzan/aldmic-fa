<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFavoritesTable extends Migration
{
    public function up()
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('imdb_id', 20);
            $table->string('title');
            $table->string('year', 20)->nullable();
            $table->string('type', 30)->nullable();
            $table->string('poster')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'imdb_id']);
            $table->index('imdb_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('favorites');
    }
}
