<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->string('spotify_album_id')->primary();
            $table->string('title');
            $table->string('cover_url');
            $table->date('release_date');
            $table->string('spotify_artist_id');

            $table->foreign('spotify_artist_id')
                ->references('spotify_artist_id')
                ->on('artists')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('albums');
    }
};
