<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'date', 'source'];


    public function users()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'news_id', 'user_id');
    }

    public function bookmarks()
    {
        return $this->hasMany(Bookmark::class);
    }
}
