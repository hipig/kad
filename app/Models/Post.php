<?php

namespace App\Models;

use EloquentFilter\Filterable;

class Post extends Model
{
    use Filterable;

    protected $fillable = [
        'content',
        'visible_status'
    ];

    protected $casts = [
        'published_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function images()
    {
        return $this->belongsToMany(Upload::class, 'post_has_images', 'post_id', 'upload_id')->withTimestamps();
    }
}
