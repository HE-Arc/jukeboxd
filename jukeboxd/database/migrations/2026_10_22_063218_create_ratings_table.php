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
        Schema::create('ratings', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('spotify_album_id');
            $table->decimal('rating', 2, 1);
            $table->boolean('is_favorite')->default(false);

            $table->foreign('spotify_album_id')
                ->references('spotify_album_id')
                ->on('albums')
                ->cascadeOnDelete();

            $table->primary(['user_id', 'spotify_album_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
