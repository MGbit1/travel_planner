<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id', 'content'];

    // 一則留言屬於一個使用者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 一則留言屬於一篇貼文
    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}