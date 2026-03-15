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
        Schema::create('place_stats', function (Blueprint $table) {
            $table->id();
            $table->string('place_id')->unique(); // Google Maps 的 place_id 或自訂 ID
            $table->string('name'); // 景點名稱
            $table->string('city')->nullable(); // 所在城市 (例如：台中市)
            $table->string('type')->nullable(); // 景點類型 (例如：餐廳、景點)
            $table->unsignedBigInteger('saved_count')->default(0); // 被加入行程的次數
            $table->unsignedBigInteger('likes_count')->default(0); // 在社群被按讚的次數
            $table->unsignedBigInteger('views_count')->default(0); // 被查看的次數
            $table->integer('score')->default(0); // 系統計算的綜合熱度分數
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('place_stats');
    }
};
