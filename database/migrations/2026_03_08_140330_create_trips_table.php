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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            // 💡 綁定會員：知道是哪個使用者存的行程，如果使用者刪除帳號，行程也跟著刪除
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            $table->string('title'); // 行程標題
            $table->json('itinerary_data'); // 存放所有天數的景點與規劃資料
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
