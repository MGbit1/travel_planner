<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    // 允許寫入這兩個欄位
    protected $fillable = ['title', 'itinerary_data'];

    // 自動把陣列轉換成 JSON 存入資料庫
    protected $casts = [
        'itinerary_data' => 'array',
    ];
}