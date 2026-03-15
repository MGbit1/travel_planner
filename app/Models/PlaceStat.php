<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlaceStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id', 'name', 'city', 'type', 'saved_count', 'likes_count', 'views_count', 'score'
    ];
}