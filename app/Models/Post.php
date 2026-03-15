<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'trip_id', 'title', 'content', 'image_url', 'days_count', 'views_count'
    ];

    // 一篇貼文屬於一個發文者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 一篇貼文可能關聯到一個具體的行程 (Trip)
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    // 一篇貼文有很多留言
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    // 一篇貼文有很多按讚紀錄
    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }
}