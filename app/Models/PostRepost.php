<?php

namespace App\Models;


class PostRepost extends Model
{
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function repostedPost()
    {
        return $this->belongsTo(Post::class, 'reposted_post_id');
    }
}
