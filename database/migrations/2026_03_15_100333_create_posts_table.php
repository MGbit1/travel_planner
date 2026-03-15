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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 發文者
            $table->foreignId('trip_id')->nullable()->constrained()->onDelete('set null'); // 關聯的行程 (可為空)
            $table->string('title'); // 貼文標題
            $table->text('content')->nullable(); // 旅遊心得
            $table->string('image_url')->nullable(); // 封面圖片
            $table->integer('days_count')->default(1); // 行程天數
            $table->unsignedBigInteger('views_count')->default(0); // 瀏覽次數
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
