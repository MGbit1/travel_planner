<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('place_name');
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->float('rating')->nullable();
            $table->string('image_url', 1000)->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'place_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wishlists');
    }
};
