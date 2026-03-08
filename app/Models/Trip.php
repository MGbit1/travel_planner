<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    // 允許被批量寫入的欄位
    protected $fillable = [
        'user_id', 
        'title', 
        'itinerary_data'
    ];

    // 💡 Laravel 魔法：自動把 JSON 轉成 PHP 陣列
    protected $casts = [
        'itinerary_data' => 'array',
    ];

    // 設定關聯：一趟行程屬於一個使用者
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}